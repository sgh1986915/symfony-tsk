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
use Symfony\Component\Yaml\Dumper;

class ExportCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('tsk:ruler:export_rules')
        ->setDefinition(array(
            new InputOption('context', 'c', InputOption::VALUE_OPTIONAL, 'Context'),
        ))
        ->setDescription('Exports rules to stdout')
        ->setHelp(<<<EOT
The <info>tsk:ruler:export_rules</info> command exports data stored in rules tables to a yaml file.

EOT
);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'TSK Ruler Export Data');   
        $output->writeln(array(
            '', 
            'This script will export data from the rules tables to a yaml file',
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
            $context = $input->getOption('context');
            $manager = $this->getContainer()->get('doctrine.orm.entity_manager');
            if ($context) {
                $method = 'findBy';
                $ruleContextRepo = $manager->getRepository('TSKRulerBundle:RuleContext');
                $ruleContext = $ruleContextRepo->findOneBy(array('name' => $context));
                if (!$ruleContext) {
                    throw new \Exception('No rule context [' . $context . ']');
                }
                $clauses['context'] = $ruleContext;
                $this->context = $ruleContext->getName();
            } else {
                $method = 'findAll';
                $clauses = array();
            }

            $this->rules = array();
            $rcRepo = $manager->getRepository('TSKRulerBundle:RuleCollection'); 
            $ruleCollections = $rcRepo->{$method}($clauses);
            if ($ruleCollections) {
                foreach ($ruleCollections as $rc) {
                    $idx = 0;
                    foreach ($rc->getRuleGroups() as $ruleGroup) {
                        $this->processRuleGroup($ruleGroup, $idx++, $rc->getContext()->getName(), $rc->getDescription());
                    }
                }

                ld($this->rules);
                $dumper = new Dumper();
                $yaml = $dumper->dump($this->rules, 2);
                file_put_contents('/Users/mhill/projects/tsk-erp-system/src/TSK/RulerBundle/Resources/files/rules.yml', $yaml);
                print $yaml;
            } else {
                print 'no rule collections';
            }
        } catch (\Exception $e) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $e->getMessage(), 'bg=red;fg=white');   
        }

        if (!empty($results['errors'])) {
            $dialog = $this->getDialogHelper();
            $dialog->writeSection($output, $results['errors'][0], 'bg=yellow;fg=white');   
        }
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
        $b['description'] = $ruleGroup->getDescription();
        $b['clause'] = 'IF ' . join(' ' . $ruleGroup->getConjunction(). ' ', $subRules) . ' THEN ' . join(' AND ', $subRewards);
        $this->rules[$contextName]['rule_groups'][$idx]['description'] = $description;
        $this->rules[$contextName]['rule_groups'][$idx]['proposition'] = $b;
    }
}
