<?php

namespace TSK\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ScheduleCategory
 *
 * @ORM\Table(name="tsk_schedule_category")
 * @ORM\Entity
 */
class ScheduleCategory
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_category_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=50, nullable=true)
     */
    private $color;


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
     * @return ScheduleCategory
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
     * Set color
     *
     * @param string $color
     * @return ScheduleCategory
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    public function __toString()
    {
        if (empty($this->name)) {
            return '<new category>';
        }
        return $this->name;
    }
 
    /**
     * Get organization.
     *
     * @return organization.
     */
    public function getOrganization()
    {
        return $this->organization;
    }
 
    /**
     * Set organization.
     *
     * @param organization the value to set.
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
        return $this;
    }
}
