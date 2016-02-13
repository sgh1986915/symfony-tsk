<?php

namespace TSK\ScheduleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Roster
 *
 * @ORM\Table(name="tsk_schedule_roster", indexes={@ORM\Index(name="start_idx", columns={"start"}), @ORM\Index(name="until_idx", columns={"until"})}, uniqueConstraints={@ORM\UniqueConstraint(name="roster_uniq", columns={"fk_student_id", "fk_class_id", "fk_schedule_entity_id"})})
 * @ORM\Entity
 */
class Roster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="roster_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ClassBundle\Entity\Classes", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_class_id", referencedColumnName="class_id", nullable=false)
     * @Assert\Type(type="TSK\ClassBundle\Entity\Classes")
     */
    private $class;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleEntity", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_schedule_entity_id", referencedColumnName="schedule_entity_id", nullable=false)
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleEntity")
     */
    private $schedule;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id", nullable=false, onDelete="CASCADE")
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="date")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="until", type="date")
     */
    private $until;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;


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
     * Set until
     *
     * @param \DateTime $until
     * @return Roster
     */
    public function setUntil(\DateTime $until)
    {
        $this->until = $until;

        return $this;
    }

    /**
     * Get until
     *
     * @return \DateTime 
     */
    public function getUntil()
    {
        return $this->until;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Roster
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }
 
    /**
     * Get start.
     *
     * @return start.
     */
    public function getStart()
    {
        return $this->start;
    }
 
    /**
     * Set start.
     *
     * @param start the value to set.
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;
        return $this;
    }
 
    /**
     * Get class.
     *
     * @return class.
     */
    public function getClass()
    {
        return $this->class;
    }
 
    /**
     * Set class.
     *
     * @param class the value to set.
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }
 
    /**
     * Get schedule.
     *
     * @return schedule.
     */
    public function getSchedule()
    {
        return $this->schedule;
    }
 
    /**
     * Set schedule.
     *
     * @param schedule the value to set.
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }
 
    /**
     * Get student.
     *
     * @return student.
     */
    public function getStudent()
    {
        return $this->student;
    }
 
    /**
     * Set student.
     *
     * @param student the value to set.
     */
    public function setStudent($student)
    {
        $this->student = $student;
        return $this;
    }
}
