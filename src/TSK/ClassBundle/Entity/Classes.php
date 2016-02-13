<?php

namespace TSK\ClassBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;
use APY\DataGridBundle\Grid\Mapping as GRID;


/**
 * Classes
 *
 * @ORM\Table(name="tsk_class")
 * @ORM\Entity
 */
class Classes
{
    /**
     * @var integer
     *
     * @ORM\Column(name="class_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @var integer
     *
     * @ORM\Column(name="tokens", type="integer", nullable=true)
     */
    private $tokens=0;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule_color", type="string", length=20, nullable=true)
     */
    private $scheduleColor;


    /**
     * @var string
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive=false;

    /**
     * @ORM\OneToMany(targetEntity="TSK\ClassBundle\Entity\ClassTypeCredit", mappedBy="class", cascade={"all"})
     * @ORM\JoinTable(name="tsk_class_type_credit",
     *      joinColumns={@ORM\JoinColumn(name="fk_class_id", referencedColumnName="class_id")}
     *  )
     * */
    protected $classTypeCredits;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ScheduleBundle\Entity\ScheduleEntity", mappedBy="classes")
     */
    protected $schedules;

    public function __construct()
    {
        // Initialize classTypeCredits
        $this->classTypeCredits = new ArrayCollection();
        $this->schedules = new ArrayCollection();
        // $ctc = new ClassTypeCredit();
        // $ctc->setClass($this);
        // $ctc->setValue(0);
        // $this->addCtc($ctc);
        // $this->addCtc($ctc);
    }


    public function getClassTypeCredits()
    {
        return $this->classTypeCredits;
        // $results = new ArrayCollection();
        // if (count($this->classTypeCredits)) {
        //     foreach ($this->classTypeCredits as $ctc) {
        //         $results[] = $ctc;
        //     }
        // }
        // return $results;
    }

    public function setClassTypeCredits($classTypeCredits)
    {
        foreach ($classTypeCredits as $idx => $ctc) {
            $this->addCtc($ctc);
        }
    }

    public function addCtc(ClassTypeCredit $classTypeCredit)
    {
        $this->classTypeCredits[] = $classTypeCredit;
    }

    public function removeCtc(ClassTypeCredit $classTypeCredit)
    {
        return $this->classTypeCredits->removeElement($classTypeCredit);
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
     * setId 
     * 
     * @param mixed $id 
     * @return Classes
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Classes
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Classes
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
     * Set is_admin
     *
     * @param boolean $isActive
     * @return User
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get is_admin
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set tokens
     *
     * @param integer $quantity
     * @return ClassToken
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    
        return $this;
    }

    /**
     * Get tokens
     *
     * @return integer 
     */
    public function getTokens()
    {
        return $this->tokens;
    }


    public function __toString()
    {
        if ($this->getTitle()) {
            return $this->getTitle();
        } else {
            return '<new class>';
        }
    }

 
    /**
     * Get scheduleColor.
     *
     * @return scheduleColor.
     */
    public function getScheduleColor()
    {
        return $this->scheduleColor;
    }
 
    /**
     * Set scheduleColor.
     *
     * @param scheduleColor the value to set.
     */
    public function setScheduleColor($scheduleColor)
    {
        $this->scheduleColor = $scheduleColor;
        return $this;
    }
}
