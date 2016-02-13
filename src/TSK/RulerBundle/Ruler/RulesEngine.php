<?php
namespace TSK\RulerBundle\Ruler;

use Ruler\RuleBuilder;
use Ruler\RuleSet;
use Ruler\Variable;
use Ruler\Rule;
use Ruler\Operator;
use Ruler\Context;

use Doctrine\ORM\EntityManager;
use TSK\RulerBundle\Ruler\RulesEngineInterface;
use TSK\RulerBundle\Model\RuleGroup;
use TSK\RulerBundle\Model\RuleContext;
use TSK\RulerBundle\Model\Rule as RuleModel;
use Symfony\Component\Yaml\Parser;
use TSK\RulerBundle\Util\PropositionParser;

abstract class RulesEngine implements RulesEngineInterface
{
    protected $em;
    protected $debug = true;
    protected $logger;
    private $rewardsEngine;

    public function __construct(EntityManager $em, $rewardsEngine, $debug = false)
    {
        $this->em = $em;
        $this->rewardsEngine = $rewardsEngine;
        $this->debug = $debug;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * buildContext 
     * 
     * @param mixed $student 
     * @access public
     * @return void
     */
    abstract public function buildContext($obj);

    public function getRuleCollections(RuleContext $ruleContext)
    {
        $rcRepo = $this->em->getRepository('TSK\RulerBundle\Entity\RuleCollection');
        $ruleCollections = $rcRepo->findBy(array('context' => $ruleContext));
        return $ruleCollections;
    }

    /**
     * applyRules 
     * 
     * @param $obj
     * @access public
     * @return void
     */
    public function xapplyRules($ruleContextName, $obj)
    {
        $this->logger->debug('In applyRules for ' . $ruleContextName);
        $affirmatives = 0;
        $ruleContext = $this->getRuleContextByName($ruleContextName);
        $ruleCollections = $this->getRuleCollections($ruleContext);
        if (count($ruleCollections)) {
            foreach ($ruleCollections as $ruleCollection) {
                $ruleGroups = $ruleCollection->getRuleGroups();
                if (count($ruleGroups)) {
                    $ctx = $this->buildContext($obj);
                    foreach ($ruleGroups as $ruleGroup) {
                        if ($this->evaluateRuleGroup($ruleGroup, $ctx)) {
                            foreach ($ruleGroup->getRewards() as $reward) {
                                $this->applyReward($reward, $ctx);
                            }
                        }
                    }
                }
            }
        }
    }

    public function evaluateProposition($proposition, $ctx)
    {
        $pp = new PropositionParser($this->em);
        list($rules, $rewards) = $pp->parse($proposition);
        if (count($rules)) {
            foreach ($rules as $rule) {
                if ($this->evaluateRule($rule, $ctx)) {
                    if (count($rewards)) {
                        foreach ($rewards as $reward) {
                            $this->applyReward($reward, $ctx);
                        }
                    }
                }
            }
        }
    }

    public function applyRules($ruleContextName, $obj)
    {
        $file = '/Users/mhill/projects/tsk-erp-system/src/TSK/RulerBundle/Resources/files/rules.yml';
        $blah = file_get_contents($file);
        $parser = new Parser();
        $out = $parser->parse($blah);
        if (!empty($out[$ruleContextName])) {
            $ruleGroups = $out[$ruleContextName]['rule_groups'];
            $ctx = $this->buildContext($obj);
            foreach ($ruleGroups as $ruleGroup) {
                $this->evaluateProposition($ruleGroup['propositions'], $ctx);
            }
        } else {
            throw new \Exception("Invalid ruleContextName $ruleContextName");
        }
    }
    
    public function getRuleContextByName($name)
    {
        $rcRepo = $this->em->getRepository('TSK\RulerBundle\Entity\RuleContext');
        $ruleContext = $rcRepo->findOneBy(array('name' => $name));
        if (!$ruleContext) {
            throw new \Exception('Rule Context [' . $name . '] Not Found');
        }
        return $ruleContext;
    }

    /**
     * evaluateRuleGroup 
     * 
     * @param RuleGroup $ruleGroup 
     * @param Context $ctx 
     * @access private
     * @return void
     */
    private function evaluateRuleGroup(RuleGroup $ruleGroup, Context $ctx)
    {
        $affirmatives = 0;
        $rules = $ruleGroup->getRules();
        if (count($rules)) {
            foreach ($rules as $rule) {
                if ($this->evaluateRule($rule, $ctx)) {
                    $affirmatives++;
                }
            }
        }
        // if we are using the AND conjunction then every rule
        // should evaluate to true
        // if we are using the OR conjunction then we only need
        // one rule to evalute to true
        if ($ruleGroup->getConjunction() == 'and') {
            return ($affirmatives == count($rules));
        } elseif ($ruleGroup->getConjunction() == 'or') {
            return ($affirmatives > 0);
        }
    }

    /**
     * evaluateRule 
     * 
     * @param RuleModel $rule 
     * @param Context $ctx 
     * @access private
     * @return void
     */
    private function evaluateRule(RuleModel $rule, Context $ctx)
    {
        $propositionClass = sprintf("Ruler\\Operator\\%s", ucfirst($rule->getComparator()));
        if (!class_exists($propositionClass)) {
            throw new \Exception("Class $propositionClass does not exist");
        }
        
        // If fact is a date, we must convert value to unix timestamp
        // to do comparison
        if (preg_match('/date/i', $rule->getFact()->getName())) {
            $date = new \DateTime($rule->getValue());
            $value = $date->getTimestamp();
        } else {
            $value = $rule->getValue();
        }
        
        $proposition = new $propositionClass(
            new Variable(
                $rule->getFact()->getName()
            ),
           new Variable('x' . $rule->getFact()->getName(), $value)
        );
        // add x value to context
        $myRule = new Rule(
            $proposition,
            function () {
            }
        );

        if ($myRule->evaluate($ctx)) {
            if ($this->debug) {
                if ($rule->getId()) {
                    $this->logger->debug("rule ". $rule->getId()." passed!");
                }
            }
            return true;
        } else {
            if ($this->debug) {
                if ($rule->getId()) {
                    $this->logger->debug("rule ". $rule->getId() ." failed");
                }
            }
            return false;
        }
    }

    /**
     * applyReward 
     * This method applies the reward specified
     * 
     * @param mixed $reward 
     * @param mixed $context 
     * @access public
     * @return void
     */
    public function applyReward($reward, $context)
    {
        $rewardsEngine = $this->rewardsEngine;
        $rewardsEngine->setReward($reward);
        $rewardsEngine->setContext($context);

        $method = $reward->getMethod();
        if (!method_exists($rewardsEngine, $method)) {
            throw new \Exception('Method ' . $method . ' does not exist');
        }
        $rewardsEngine->{$method}($reward->getMetaData());
    }
}
