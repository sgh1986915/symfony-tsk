<?php
namespace TSK\RulerBundle\Model;

class RuleCollection
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * context 
     */
    protected $context;

    protected $description;

    /**
     * @var ArrayCollection
     */
    protected $ruleGroups;

    public function getId()
    {
        return $this->id;
    }

    public function getRuleGroups()
    {
        return $this->ruleGroups;
    }

    public function setRuleGroups($ruleGroups)
    {
        foreach ($ruleGroups as $idx => $ruleGroup) {
            $this->addRuleGroup($ruleGroup);
        }
    }

    public function addRuleGroup(RuleGroup $ruleGroup)
    {
        $this->ruleGroups[] = $ruleGroup;
    }

    public function removeRuleGroup(RuleGroup $ruleGroup)
    {
        return $this->ruleGroups->removeElement($ruleGroup);
    }
 
    /**
     * Get context.
     *
     * @return context.
     */
    public function getContext()
    {
        return $this->context;
    }
 
    /**
     * Set context.
     *
     * @param context the value to set.
     */
    public function setContext(RuleContext $context)
    {
        $this->context = $context;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }
}
