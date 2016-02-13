<?php
namespace TSK\RulerBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Fact
 *
 */
class Fact
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @Assert\Type(type="TSK\RulerBundle\Model\RuleGroup")
     */
    protected $ruleGroup;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $factType;


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
     * setId 
     * 
     * @param integer $id 
     * @access public
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Fact
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Fact
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set method
     *
     * @param string $method
     * @return Fact
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get factType.
     *
     * @return factType.
     */
    public function getFactType()
    {
        return $this->factType;
    }
 
    /**
     * Set factType.
     *
     * @param factType the value to set.
     */
    public function setFactType($factType)
    {
        $this->factType = $factType;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }

    public function getOrganization()
    {
        return $this->organization;
    }
    
    public function setOrganization(\TSK\UserBundle\Entity\Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }
 
    /**
     * Get ruleGroup.
     *
     * @return ruleGroup.
     */
    public function getRuleGroup()
    {
        return $this->ruleGroup;
    }
 
    /**
     * Set ruleGroup.
     *
     * @param ruleGroup the value to set.
     */
    public function setRuleGroup(RuleGroup $ruleGroup)
    {
        $this->ruleGroup = $ruleGroup;
        return $this;
    }
}
