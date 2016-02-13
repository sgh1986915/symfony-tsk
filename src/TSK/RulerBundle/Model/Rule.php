<?php
namespace TSK\RulerBundle\Model;

/**
 * Storage agnostic Rule object
 * 
 */
class Rule
{
    /**
     * @var integer
     */
    protected $id;

    /**
    */
    protected $group;

    /**
     *
     */
    protected $fact;

    /**
     * @var string
     */
    protected $comparator;

    /**
     * @var string
     */
    protected $value;

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
     * Set comparator
     *
     * @param string $comparator
     * @return Proposition
     */
    public function setComparator($comparator)
    {
        $this->comparator = $comparator;

        return $this;
    }

    /**
     * Get comparator
     *
     * @return string 
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return Proposition
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }
 
    /**
     * Get group.
     *
     * @return group.
     */
    public function getGroup()
    {
        return $this->group;
    }
 
    /**
     * Set group.
     *
     * @param group the value to set.
     */
    public function setGroup(RuleGroup $group)
    {
        $this->group = $group;
        return $this;
    }

    public function getFact()
    {
        return $this->fact;
    }

    public function setFact(Fact $fact)
    {
        $this->fact = $fact;
        return $this;
    }
}
