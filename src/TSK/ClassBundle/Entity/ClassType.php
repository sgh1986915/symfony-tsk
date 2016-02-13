<?php

namespace TSK\ClassBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ClassType
 *
 * @ORM\Table(name="tsk_ref_class_type")
 * @ORM\Entity
 */
class ClassType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="class_type_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="class_type_name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * setId 
     * 
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * Set name
     *
     * @param string $name
     * @return ClassType
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

    public function getOrganization()
    {
        return $this->organization;
    }
    
    public function setOrganization(\TSK\UserBundle\Entity\Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
