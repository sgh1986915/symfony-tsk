<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * States
 *
 * @ORM\Table(name="tsk_ref_states")
 * @ORM\Entity
 */
class States
{
    /**
     * @var integer
     *
     * @ORM\Column(name="state_id", type="string", length=2)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="state_name", type="string", length=50)
     */
    private $state_name;


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
     * @param string $id 
     * @return States
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set state_name
     *
     * @param string $stateName
     * @return States
     */
    public function setStateName($stateName)
    {
        $this->state_name = $stateName;
    
        return $this;
    }

    /**
     * Get state_name
     *
     * @return string 
     */
    public function getStateName()
    {
        return $this->state_name;
    }

    public function __toString()
    {
        return $this->id;
    }
}
