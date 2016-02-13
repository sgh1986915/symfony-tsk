<?php
namespace TSK\RulerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Symfony\Component\Finder\Finder;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Yaml\Parser;
use TSK\RulerBundle\Entity\RuleCollection;
use TSK\RulerBundle\Entity\RuleGroup;
use TSK\RulerBundle\Entity\Rule;
use TSK\RulerBundle\Entity\Reward;

class ImportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('tsk:ruler:import_rules')
        ->setDefinition(array(
        ))
        ->addArgument('file', InputArgument::REQUIRED, 'Rules file to import')
        ->setDescription('Imports rules from rules file')
        ->setHelp(<<<EOT
The <info>tsk:ruler:import_rules</info> command imports rules from a properly-formatted yaml file.

EOT
);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'TSK Ruler Import Data');
        $output->writeln(array(
            '', 
            'This script will import rules data from a file',
            '' 
        )); 
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
            $dialog = $this->getDialogHelper();
            $file = $input->getArgument('file');
            $this->manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            $ruleContextRepo =  $this->manager->getRepository('TSKRulerBundle:RuleContext');
            if ($file) {
                $blah = file_get_contents($file);
                $parser = new Parser();
                $out = $parser->parse($blah);

                $ruleCount = $rewardCount = 0;
                foreach($out as $context => $arr) {
                    $dialog->writeSection($output, "Process $context context ...", 'bg=blue;fg=white');
                    $ctx = $ruleContextRepo->findOneBy(array('name' => $context));
                    // create ruleContext with name $context
                    if (!$ctx) {
                        throw new \Exception('Cannot find context ['.$context.']');
                    }
                    if (!empty($arr['rule_groups'])) {
                        $rc = new RuleCollection();
                        $rc->setContext($ctx);
                        foreach ($arr['rule_groups'] as $ruleGroup) {
                            if (!empty($ruleGroup['proposition'])) {
                                list($ruleContainers, $rewards) = $this->parseProposition($ruleGroup['proposition']);
                                
                                $rg = new RuleGroup();
                                foreach ($ruleContainers as $ruleContainer) {
                                    // ld($ruleContainer); exit;
                                    $rg->addRule($ruleContainer['rule']);
                                    $ruleContainer['rule']->setGroup($rg);
                                    $rg->setDescription($ruleContainer['description']);
                                    $ruleCount++;
                                }
                                foreach ($rewards as $reward) {
                                    $rg->addReward($reward);
                                    $reward->setGroup($rg);
                                    $rewardCount++;
                                }
                                $rg->setCollection($rc);
                            }
                            if (!empty($ruleGroup['description'])) {
                                $rc->setDescription($ruleGroup['description']);
                            }
                            $this->manager->persist($rg);
                        }
                        $this->manager->persist($rc);
                        $this->manager->flush();
                    }
                }
            }
        } catch (\Exception $e) {
            $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');   
        }

        if (!empty($results['errors'])) {
            $dialog->writeSection($output, $results['errors'][0], 'bg=yellow;fg=white');   
        }
        $dialog->writeSection($output, "$ruleCount Rules and $rewardCount Rewards loaded!!", 'bg=green;fg=white');
    }

    protected function processRuleGroup($ruleGroup, $idx, $contextName, $description='')
    {
        $i = 0;
        $subRules = $subRewards = array();
        foreach ($ruleGroup->getRules() as $rule) {
            $subRules[] = $rule->getFact()->getName() .' ' .$rule->getComparator() . ' ' . $rule->getValue();
            $i++;
        }

        $j = 0;
        foreach ($ruleGroup->getRewards() as $reward) {
            $subRewards[] = $reward->getMethod() . '(' . $reward->getMetaData() . ')';
        }
        $b = 'IF ' . join(' ' . $ruleGroup->getConjunction(). ' ', $subRules) . ' THEN ' . join(' AND ', $subRewards);
        $this->rules[$contextName]['rule_groups'][$idx]['proposition'] = $b;
        $this->rules[$contextName]['rule_groups'][$idx]['description'] = $description;
    }

    public function parseProposition($proposition)
    {
        $description = $proposition['description'];
        $proposition = $proposition['clause'];
        $proposition = preg_replace('/^IF /', '', $proposition);
        list($rules, $rewards) = preg_split('/ THEN /i', $proposition);
        $ruleParts = preg_split('/ and /i', $rules); 
        foreach ($ruleParts as $rulePart) {
            list($fact, $comparator, $value) = preg_split('/\s/', $rulePart);
            $rule = new Rule();
            $factRepo = $this->manager->getRepository('TSKRulerBundle:Fact');
            $myFact = $factRepo->findOneBy(array('name' => $fact));
            if (!$myFact) {
                throw new \Exception('No fact [' . $fact . ']');
            }
            $rule->setFact($myFact);
            $rule->setComparator($comparator);
            $rule->setValue($value);
            $myRules[] = array('rule' => $rule, 'description' => $description);
        }

        $rewardParts = preg_split('/ and /i', $rewards);
        foreach ($rewardParts as $rewardPart) {
            preg_match('/(\w+)\((\w+)?\)/', $rewardPart, $matches);
            if (count($matches) == 2) {
                $method = $matches[1];
                $meta = null;
            } else if (count($matches) == 3) {
                $method = $matches[1];
                $meta = $matches[2];
            } else {
                ld($matches);
                print "nope [$rewardPart]" . count($matches); exit;
            }
            $reward = new Reward();
            $reward->setMethod($method);
            $reward->setMetaData($meta);
            $myRewards[] = $reward;
        }
        return array($myRules, $myRewards);
    }
}
