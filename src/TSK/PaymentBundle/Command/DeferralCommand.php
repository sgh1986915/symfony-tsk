<?php
namespace TSK\PaymentBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use TSK\PaymentBundle\Entity\PaymentsDeferred;
use TSK\PaymentBundle\Util\Deferral;

class DeferralCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('tsk:payment:process_deferrals')
        ->setDefinition(array(
            new InputOption('since', 'S', InputOption::VALUE_REQUIRED, 'Only process payments since this date (i.e. yyyy-mm-dd)'),
            new InputOption('payment-id', 'p', InputOption::VALUE_REQUIRED, 'Only process the single payment specified by payment-id'),
            new InputOption('dry-run', 'N', InputOption::VALUE_NONE, 'Do not write any data, only print debug'),
            new InputOption('max', 'M', InputOption::VALUE_NONE, 'Defer at most "max" payments')
        ))
        ->setDescription('process deferrals')
        ->setHelp(<<<EOT
The <info>tsk:payment:process_deferrals</info> command calculates and stores deferrals of payments. 

You may pass a `since` argument containing a date in yyyy-mm-dd format which we be used to filter processed payments:

<info>php app/console tsk:payment:process_deferrals --since 2013-05-01</info>

Supplying a `payment-id` argument will allow for processing a single paymentDo not write any data, only print debug

You may also pass a `dry-run` argument that will output the queries that it would run without actually making any database changes:

<info>php app/console tsk:payment:process_deferrals --dry-run</info>

To deactivate the interaction mode, simply use the `--no-interaction` option without forgetting to pass all needed options:

<info>php app/console tsk:payment:process_deferrals --since 2012-01-01 --no-interaction</info>

EOT
);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'TSK Payment Deferrals');   
        $output->writeln(array(
            '', 
            'This script will defer any payments according to your parameters',
            '' 
        )); 
    }
    
    public function validateSince($since)
    {
        if (checkdate($since)) {
            return $since;
        } else {
            throw new \InvalidArgumentException('Since must be a valid date');
        }
    }

    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $since = $input->getOption('since');
            $dry_run = $input->getOption('dry-run');
            $paymentID = $input->getOption('payment-id');
            $max = (int) $input->getOption('max');
            $MAX = 20;
            if (($max > 0) && ($max < $MAX)) {
                // nada
            } else {
                $max = $MAX;
            }
            $clauses[] = 'p.deferralTimestamp IS NULL';
            $params = array();
            if ($since) {
                $clauses[] = 'p.payment_date > :since';
                $params[':since'] = $since;
            }
            if ($paymentID) {
                $clauses[] = 'p.id = :id';
                $params[':id'] = $paymentID;
            }

            $em = $this->getContainer()->get('doctrine')->getManager();
    
            $query = $em->createQuery('SELECT p from TSK\PaymentBundle\Entity\Payment p WHERE ' . join(' AND ', $clauses));
            $query->setParameters($params);
            $query->setFirstResult(0);
            $query->setMaxResults($max);
            $payments = $query->getResult();
            
            if (count($payments)) {
                foreach ($payments as $payment) {
                    $appliedCharges = $payment->getChargePayments();
                    if ($appliedCharges->count()) {
                        foreach ($appliedCharges as $chargePayment) {
                            $charge = $chargePayment->getCharge();
                            // Is this charge of type tuition?  We only defer
                            // the portion of the payment applied to tuition
                            if ($charge->getAccount()->getName() == 'Inc Fm Students') {
                                // get contract details
                                $contracts = $charge->getContracts();
                                $contract = $contracts[0];
                                $contractAmount = $contract->getAmount();
                                $contractStartDate = $contract->getCreatedDate()->format('YYYY-mm-dd');
                                $deferralDuration = $contract->getDeferralDurationMonths();
                                $deferralDistributionStrategy = $contract->getDeferralDistributionStrategy();
                                $deferralDistributionStrategy = 'accelerated';
                                if (!in_array($deferralDistributionStrategy, array('straight', 'accelerated'))) {
                                    throw new \Exception('Unrecognized deferral distribution strategy ' . $deferralDistributionStrategy);
                                }
                                
                                $deferralRate = $contract->getDeferralRate();

                                // Sum any REALIZED payments for this contract and group by year-month ...

                                // Sum any pre-existing deferral payments for this contract and group by year-month 
                                // Could have done this in mysql w/ the following query, but Doctrine doesn't support it.
                                // select year(date_realized), month(date_realized), sum(amount) from tsk_payments_deferred where fk_contract_id=6 group by year(date_realized), month(date_realized) order by year(date_realized) asc, month(date_realized) asc
                                $query = $em->createQuery('SELECT p from TSK\PaymentBundle\Entity\PaymentsDeferred p WHERE p.contract=:contract ORDER BY p.dateRealized');
                                $query->setParameters(array(':contract' => $contract));
                                $deferments = $query->getResult();
                                $initialDeferrals = array();
                                foreach ($deferments as $deferment) {
                                    $key = $deferment->getDateRealized()->format('Y-m');
                                    if (empty($initialDeferrals[$key])) {
                                        $initialDeferrals[$key] = $deferment->getAmount();
                                    } else {
                                        $initialDeferrals[$key] += $deferment->getAmount();
                                    }
                                }

                                $inits = array_values($initialDeferrals);
                                if (!$inits) {
                                    $inits = array_fill(0, $contract->getDeferralDurationMonths(), 0);
                                }
                            
                                // Set deferral schedule
                                $d = new Deferral(
                                        $contract->getAmount(),
                                        $contract->getDeferralRate(),
                                        $contract->getDeferralDurationMonths(),
                                        $inits,
                                        $contract->getContractStartDate()
                                        );

                                // RUN THE DEFERRALS!!
                                $deferralMethod = ($deferralDistributionStrategy == 'straight') ? 'distributePaymentEvenly' : 'distributePaymentMax';
                                $deferrals = $d->{$deferralMethod}(
                                        $chargePayment->getAmount(),
                                        $contract->getRemainingDeferralPeriods()
                                        );
                                $Deferrals = $d->datestampPayments($deferrals);

                                if ($dry_run) {
                                    print "schedule =\n";
                                    ladybug_dump($d->getSchedule());
                                    print "initial deferrals\n";
                                    ladybug_dump($inits);
                                    print "deferrals\n";
                                    $summary = sprintf('<options=bold>$%5.2f</options=bold> Deferred at <options=bold>%3.2f</options=bold> over <options=bold>%d</options=bold> months using <options=bold>"%s"</options=bold> Strategy starting on <options=bold>%s</options=bold>', 
                                        $chargePayment->getAmount(), 
                                        $contract->getDeferralRate(),
                                        $contract->getRemainingDeferralPeriods(),
                                        $contract->getDeferralDistributionStrategy(),
                                        $contract->getContractStartDate()->format('Y-m-d')
                                    );
                                    $formatter = $this->getHelperSet()->get('formatter');
                                    $formattedLine = $formatter->formatSection('Deferral Summary', $summary);
                                    $output->writeln($formattedLine);
                                    ladybug_dump($Deferrals);
                                    print count($Deferrals) . " payments totalling ";
                                    print array_sum(array_values($Deferrals)) . "\n";
                                } else {
                                    foreach ($Deferrals as $DeferralDate => $DeferralAmount) {
                                        if ($DeferralAmount) {
                                            $paymentDeferred = new PaymentsDeferred();
                                            $paymentDeferred->setPayment($payment);
                                            $paymentDeferred->setAmount($DeferralAmount);
                                            $paymentDeferred->setDateRealized(new \DateTime($DeferralDate));
                                            $paymentDeferred->setContract($contract);
                                            $em->persist($paymentDeferred);
                                        }
                                        // we actually save the deferred payment each time ...
                                        $em->flush();
                                    }

                                }
                            }
                        }

                        if (!$dry_run) {
                            // Update payment deferralTimestamp
                            $payment->setDeferralTimestamp(new \DateTime());
                            $em->persist($payment);
                            $em->flush();
                        }

                    } else {
                        throw new \Exception('No charges applied to payment.  Surely, this cannot be true');
                    }
                }
            } else {
                $dialog = $this->getDialogHelper();
                $dialog->writeSection($output, "No deferrable payments match your criteria, nothing to defer.", 'bg=red;fg=white');   
            }

        } catch (\PDOException $e) {
                
        } catch (\Exception $e) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');   
        }

        if (!empty($results['errors'])) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $results['errors'][0], 'bg=yellow;fg=white');   
        }
    }
}
