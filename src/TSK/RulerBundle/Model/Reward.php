<?php
namespace TSK\RulerBundle\Model;

/**
 * Storage agnostic reward object
 * Reward
 *
 */
class Reward
{
    /**
     * @var integer
     */
    protected $id;

    protected $group;

    /**
     * @var string
     *
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
    protected $metaData;


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
     * Set name
     *
     * @param string $name
     *
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
 
    public function __toString()
    {
        if ($this->getName()) {
            return $this->getName();
        } else {
            return '<new reward>';
        }
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
     * Get method.
     *
     * @return method.
     */
    public function getMethod()
    {
        return $this->method;
    }
 
    /**
     * Set method.
     *
     * @param method the value to set.
     */
    public function setMethod($method)
    {
        $this->method = $method;
        return $this;
    }
 
    /**
     * Get metaData.
     *
     * @return metaData.
     */
    public function getMetaData()
    {
        return $this->metaData;
    }
 
    /**
     * Set metaData.
     *
     * @param metaData the value to set.
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
        return $this;
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
}
