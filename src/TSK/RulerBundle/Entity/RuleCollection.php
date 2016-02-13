<?php
namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\RulerBundle\Model\RuleCollection as BaseRuleCollection;
use Doctrine\Common\Collections\ArrayCollection;
use TSK\RulerBundle\Model\RuleGroup as BaseRuleGroup;
use TSK\RulerBundle\Model\RuleContext as BaseRuleContext;

/**
 * RuleCollection 
 * 
 * @ORM\Table(name="tsk_rule_collection")
 * @ORM\Entity
 */
class RuleCollection extends BaseRuleCollection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rule_collection_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\RulerBundle\Entity\RuleContext", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rule_context_id", referencedColumnName="rule_context_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\RulerBundle\Entity\RuleContext")
     */
    protected $context;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=true)
     */
    protected $description;

    /**
     * @ORM\OneToMany(targetEntity="TSK\RulerBundle\Entity\RuleGroup", mappedBy="collection", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="fk_rule_group_id", referencedColumnName="rule_group_id")
     */
    protected $ruleGroups;

    public function __construct()
    {
        $this->ruleGroups = new ArrayCollection();
    }

    public function __toString()
    {
        return (string)$this->id;
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

    public function addRuleGroup(BaseRuleGroup $ruleGroup)
    {
        $this->ruleGroups[] = $ruleGroup;
    }

    public function removeRuleGroup(BaseRuleGroup $ruleGroup)
    {
        return $this->ruleGroups->removeElement($ruleGroup);
    }
 
    /**
     * Get description.
     *
     * @return description.
     */
    public function getDescription()
    {
        return $this->description;
    }
 
    /**
     * Set description.
     *
     * @param description the value to set.
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
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
    public function setContext(BaseRuleContext $context)
    {
        $this->context = $context;
        return $this;
    }
}
