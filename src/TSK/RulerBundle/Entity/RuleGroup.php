<?php
namespace TSK\RulerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TSK\RulerBundle\Model\RuleGroup as BaseRuleGroup;
use TSK\RulerBundle\Model\RuleGroupContext as BaseRuleGroupContext;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * RuleGroup
 * 
 * @ORM\Entity
 * @ORM\Table(name="tsk_rule_group")
 * 
 */
class RuleGroup extends BaseRuleGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rule_group_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var text
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="RuleCollection")
     * @ORM\JoinColumn(name="fk_rule_collection_id", referencedColumnName="rule_collection_id")
     */
    private $collection;

    /**
     * @var string
     *
     * @ORM\Column(name="conjunction", type="string", length=25)
     */
    protected $conjunction='and';

    /**
     * @ORM\OneToMany(targetEntity="Rule", mappedBy="group", cascade={"persist","remove"})
     **/
    protected $rules;

    /**
     * @ORM\OneToMany(targetEntity="Reward", mappedBy="group", cascade={"persist","remove"})
     **/
    protected $rewards;

    public function getId()
    {
        return $this->id;
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

    public function __toString()
    {
        $str = 'IF ';
        $ruleConditions = $ruleRewards = array();
        foreach ($this->getRules() as $rule) {
            $ruleConditions[] = sprintf("%s", $rule);
        }
        foreach ($this->getRewards() as $reward) {
            $ruleRewards[] = sprintf("%s", $reward);
        }
        return (string) $str . ' ' . join(' ' . $this->getConjunction() . ' ', $ruleConditions) . ' THEN AWARD ' . join(' and ', $ruleRewards);
    }
 
 /**
  * Get collection.
  *
  * @return collection.
  */
 function getCollection()
 {
     return $this->collection;
 }
 
 /**
  * Set collection.
  *
  * @param collection the value to set.
  */
 function setCollection(RuleCollection $collection)
 {
     $this->collection = $collection;
 }
}
