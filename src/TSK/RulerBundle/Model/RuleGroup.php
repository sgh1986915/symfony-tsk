<?php
namespace TSK\RulerBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Storage agnostic RuleGroup model
 */
class RuleGroup
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var text
     */
    protected $description;

    /**
     * @var string
     */
    protected $conjunction;

    /**
     * @var ArrayCollection
     **/
    protected $rules;

    /**
     * @var ArrayCollection
     **/
    protected $rewards;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
        $this->rewards = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set conjunction
     *
     * @param string $conjunction
     * @return RuleGroup
     */
    public function setConjunction($conjunction)
    {
        $this->conjunction = $conjunction;

        return $this;
    }

    /**
     * Get conjunction
     *
     * @return string 
     */
    public function getConjunction()
    {
        return $this->conjunction;
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function setRules($rules)
    {
        foreach ($rules as $idx => $rule) {
            $this->addRule($rule);
        }
    }

    public function addRule(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    public function addRules(Rule $rule)
    {
        $this->rules[] = $rule;
    }

    public function removeRule(Rule $rule)
    {
        return $this->rules->removeElement($rule);
    }

    public function getRewards()
    {
        return $this->rewards;
    }

    public function setRewards($rewards)
    {
        foreach ($rewards as $idx => $reward) {
            $this->addReward($reward);
        }
    }

    public function addReward(Reward $reward)
    {
        $this->rewards[] = $reward;
    }

    public function addRewards(Reward $reward)
    {
        $this->rewards[] = $reward;
    }

    public function removeReward(Reward $reward)
    {
        return $this->rewards->removeElement($reward);
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
}
