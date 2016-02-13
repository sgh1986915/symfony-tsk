<?php

namespace TSK\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ScheduleInstance
 *
 * @ORM\Table(name="tsk_schedule_instance")
 * @ORM\Entity
 */
class ScheduleInstance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_instance_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="ScheduleEntity", cascade={"persist","remove"}, inversedBy="instances")
     * @ORM\JoinColumn(name="fk_schedule_entity_id", referencedColumnName="schedule_entity_id", nullable=false)
     * @Assert\Type(type="ScheduleEntity")
     */
    protected $scheduleEntity;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\User", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_user_id", referencedColumnName="user_id", nullable=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\User")
     */
    protected $user;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    protected $school;

    /**
     * @var integer
     * Hold an optional class_id, not a true foreign key
     *
     * @ORM\Column(name="class_id", type="integer", nullable=true)
     */
    protected $classId;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime")
     */
    private $end;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_all_day", type="boolean", nullable=true)
     */
    private $isAllDay;


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
     * Set start
     *
     * @param \DateTime $start
     * @return ScheduleInstance
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return ScheduleInstance
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set isAllDay
     *
     * @param boolean $isAllDay
     * @return ScheduleInstance
     */
    public function setIsAllDay($isAllDay)
    {
        $this->isAllDay = $isAllDay;

        return $this;
    }

    /**
     * Get isAllDay
     *
     * @return boolean 
     */
    public function getIsAllDay()
    {
        return $this->isAllDay;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return ScheduleInstance
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
     * Get user.
     *
     * @return user.
     */
    public function getUser()
    {
        return $this->user;
    }
 
    /**
     * Set user.
     *
     * @param user the value to set.
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
 
    /**
     * Get scheduleEntity.
     *
     * @return scheduleEntity.
     */
    public function getScheduleEntity()
    {
        return $this->scheduleEntity;
    }
 
    /**
     * Set scheduleEntity.
     *
     * @param scheduleEntity the value to set.
     */
    public function setScheduleEntity($scheduleEntity)
    {
        $this->scheduleEntity = $scheduleEntity;
        return $this;
    }
 
    /**
     * Get school.
     *
     * @return school.
     */
    public function getSchool()
    {
        return $this->school;
    }
 
    /**
     * Set school.
     *
     * @param school the value to set.
     */
    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }
 
    /**
     * Get classId.
     *
     * @return classId.
     */
    public function getClassId()
    {
        return $this->classId;
    }
 
    /**
     * Set classId.
     *
     * @param classId the value to set.
     */
    public function setClassId($classId)
    {
        $this->classId = $classId;
        return $this;
    }
}
