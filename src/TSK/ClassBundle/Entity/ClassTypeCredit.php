<?php

namespace TSK\ClassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ClassTypeCredit
 *
 * @ORM\Table(name="tsk_class_type_credit",uniqueConstraints={@ORM\UniqueConstraint(name="class_type_credit_uniq", columns={"fk_class_id", "fk_class_type_id"})})
 * @ORM\Entity
 */
class ClassTypeCredit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="class_type_credit_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Classes", inversedBy="classTypeCredits", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_class_id", referencedColumnName="class_id", nullable=false, onDelete="CASCADE", unique=false)
     * @Assert\Type(type="\TSK\ClassBundle\Entity\Classes")
     */
    protected $class;

    /**
     *
     * @ORM\ManyToOne(targetEntity="ClassType", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_class_type_id", referencedColumnName="class_type_id", nullable=false, onDelete="CASCADE", unique=false)
     * @Assert\Type(type="\TSK\ClassBundle\Entity\ClassType")
     */
    protected $classType;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

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
     * @return ClassTypeCredit
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setClass(Classes $class)
    {
        $this->class = $class;
        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClassType(ClassType $classType)
    {
        $this->classType = $classType;
        return $this;
    }

    public function getClassType()
    {
        return $this->classType;
    }

    public function __toString()
    {
        return $this->getClass()->getTitle() . ':' . $this->getClassType()->getName() . ':' . $this->getValue() ;    
    }
}
