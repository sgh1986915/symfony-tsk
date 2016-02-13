<?php

namespace TSK\PaymentBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use TSK\PaymentBundle\Event\PaymentEvent;
use TSK\PaymentBundle\Entity\Payment;
use TSK\PaymentBundle\Entity\Journal;
use TSK\PaymentBundle\Entity\ChargePayment;
use TSK\ContractBundle\Entity\Contract;
use TSK\PaymentBundle\Util\Deferral;

class PaymentListener
{
    private $em;
    private $logger;

    public function __construct($em, $logger='')
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function onReceive(PaymentEvent $event)
    {
        $payment = $event->getPayment();
        $cpRepo = $this->em->getRepository('TSK\PaymentBundle\Entity\ChargePayment');
        $chargePayments = $cpRepo->findBy(array('payment' => $payment));
        foreach ($chargePayments as $cp) {
            if ($cp->getCharge()->getAccount()->getName() == 'Inc Fm Students') {
                $results = array();
                $contracts = $cp->getCharge()->getContracts();

                foreach ($contracts as $contract) {
                    $inits = $this->getMonthlyPrepayments($contract);
                    $today = new \DateTime();
                    $terms = $contract->getPaymentTerms();
                    $obj = json_decode($terms['paymentsData']);
                    $d = new Deferral(
                        $obj->principal, 
                        $contract->getDeferralRate(), 
                        $contract->getDeferralDurationMonths(),
                        $inits,
                        $contract->getContractStartDate()
                    );

                    $numFirsts = $this->countFirstOfMonthsSince($contract->getContractStartDate());

                    $deferrals = $d->distributePaymentMax($cp->getAmount(), $contract->getDeferralDurationMonths() - $numFirsts);
                    $Deferrals = $d->datestampPayments($deferrals);

                    $debitAccount = $payment->getPaymentMethod()->getAccount();
                    $chargeDeferralAccount = $cp->getCharge()->getDeferralAccount();
                    $chargeAccount = $cp->getCharge()->getAccount();
                    foreach ($Deferrals as $DeferralDate => $DeferralAmount) {
                        $DD = new \DateTime($DeferralDate);
                        if ($DeferralAmount) {
                            if ($DD <= $today) {
                                $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeAccount, 'debitAccount' => $debitAccount, 'debitAmount' => $DeferralAmount, 'creditAmount' => $DeferralAmount, 'chargePayment' => $cp, 'description' => $cp->getCharge()->getDescription());
                                if ($cp->getAmount() > $DeferralAmount) {
                                    $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeDeferralAccount, 'debitAccount' => $debitAccount, 'debitAmount' => $cp->getAmount() - $DeferralAmount, 'creditAmount' => $cp->getAmount() - $DeferralAmount, 'chargePayment' => $cp, 'description' => $cp->getCharge()->getDescription());
                                }
                            } else {
                                $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeAccount, 'debitAccount' => $chargeDeferralAccount, 'debitAmount' => $DeferralAmount, 'creditAmount' => $DeferralAmount, 'chargePayment' => $cp, 'description' => $cp->getCharge()->getDescription());
                            }
                        }
                    }
                }

                // Here is where we actually insert into journal ...
                if (count($results)) {
                    foreach ($results as $result) {
                        $journal = $this->generateJournal($result);
                        $this->em->persist($journal);
                    }
                    $this->em->flush();
                }
            }
        }
    }

    public function generateJournal($arr)
    {
        $cp = $arr['chargePayment'];
        $journal = new Journal();
        $journal->setSchool($cp->getCharge()->getSchool());
        $journal->setDebitAmount($arr['debitAmount']);
        $journal->setDebitAccount($arr['debitAccount']);
        $journal->setCreditAmount($arr['creditAmount']);
        $journal->setCreditAccount($arr['creditAccount']);
        $journal->setDescription($arr['description']);
        $journal->setPostedDate(new \DateTime($arr['date']));
        $journal->setCharge($cp->getCharge());
        $journal->setPayment($cp->getPayment());
        return $journal;
    }

    /**
     * getMonthlyPrepayments 
     * Takes a contract and determines how many payments have already
     * been made against charges assigned to this contract.  Payments
     * are grouped by calendar month.
     * 
     * @TODO This should be moved to Deferral Util lib
     * @param Contract $contract 
     * @access public
     * @return void
     */
    public function getMonthlyPrepayments(Contract $contract)
    {
        $buckets = array();
        $inits = array();
        // Initialize an array of $contract->getDeferralDurationMonths() buckets, indexed by month
        for ($j=0; $j < $contract->getDeferralDurationMonths(); $j++) {
            $bucketDate = clone $contract->getContractStartDate();
            $bucketDate->add(new \DateInterval('P'.$j.'M'));
            $buckets[$bucketDate->format('Y-m')] = 0;
        }

        // Query journal table to determine how much has already been realized by this contract?
        $conn = $this->em->getConnection();
        $stmt = $conn->prepare('select date_format(j.journal_date, "%m") as dmonth, year(j.journal_date) as dyear, sum(j.credit_amount) as damount from tsk_journal j inner join tsk_contract_charge cc where j.fk_credit_account_id=:credit_account_id AND cc.fk_contract_id=:contract_id and j.fk_charge_id=cc.fk_charge_id group by date_format(j.journal_date, "%m"), year(j.journal_date)');
        $stmt->bindValue(':credit_account_id', 4);
        $stmt->bindValue(':contract_id', $contract->getId());
        $stmt->execute();
        $prepayments = $stmt->fetchAll();

        // Add prepayments to buckets
        foreach ($prepayments as $pp) {
            $buckets[$pp['dyear'] .'-'.$pp['dmonth']] += $pp['damount'];
        }

        // Index bucket array by ints instead of months
        foreach ($buckets as $month => $bucketAmount) {
            $inits[] = $bucketAmount;
        }
        return $inits;
    }

    /**
     * countFirstOfMonthsSince 
     * Counts the number of "firsts" since date to today.  Used
     * for deferrals
     * 
     * @TODO This should be moved to Dates Util lib
     * @param \DateTime $date 
     * @access public
     * @return void
     */
    public function countFirstOfMonthsSince(\DateTime $date)
    {
        $today = new \DateTime();
        if ($today < $date) {
            return 0;
        }

        $delta = $date->diff($today);
        if ($delta->format('%R%a') < 800) {
            $firsts = 0;
            for ($i=1; $i < $delta->format('%a'); $i++) {
                $myDate = clone $date;
                $myDate->add(new \DateInterval('P'.$i.'D'));
                if ($myDate->format('d') == '1') {
                    $firsts++;
                }
            }
            return $firsts;
        } else {
            throw new \Exception('Date spread is too large ' . $delta->format('%R%a'));
        }
    }
}
