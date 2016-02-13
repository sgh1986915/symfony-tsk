<?php

namespace TSK\ScheduleBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleAttendance
 *
 * @ORM\Table(name="tsk_schedule_attendance", uniqueConstraints={@ORM\UniqueConstraint(name="attendance_uniq", columns={"att_date", "fk_student_id", "fk_class_id"})})
 * @ORM\Entity
 */
class ScheduleAttendance
{
    /**
     * @var integer
     *
     * @ORM\Column(name="schedule_attendance_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    private $school;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ClassBundle\Entity\Classes")
     * @ORM\JoinColumn(name="fk_class_id", referencedColumnName="class_id", nullable=false)
     * @Assert\Type(type="TSK\ClassBundle\Entity\Classes")
     */
    private $class;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleEntity")
     * @ORM\JoinColumn(name="fk_schedule_entity_id", referencedColumnName="schedule_entity_id", nullable=false)
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleEntity")
     */
    private $schedule;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student")
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id", nullable=false)
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;


    /**
     *
     * @ORM\OneToMany(targetEntity="TSK\StudentBundle\Entity\StudentCreditLog", cascade={"persist","remove"}, mappedBy="attendance")
     * @Assert\Type(type="TSK\StudentBundle\Entity\StudentCreditLog")
     */
    private $credits;

    /**
     *
     * @ORM\OneToMany(targetEntity="TSK\ContractBundle\Entity\ContractTokenDebitLog", cascade={"persist","remove"}, mappedBy="attendance")
     * @Assert\Type(type="TSK\ContractBundle\Entity\ContractTokenDebitLog")
     */
    private $tokens;

    /**
     * attendanceDate
     * @var \DateTime
     *
     * @ORM\Column(name="att_date", type="date")
     */
    private $attDate;

    /**
     * status
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    /**
     * notes
     * @var string
     *
     * @ORM\Column(name="notes", type="string", nullable=true)
     */
    private $notes;

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
     * Set attDate
     *
     * @param \DateTime $attDate
     * @return ScheduleAttendance
     */
    public function setAttDate($attDate)
    {
        $this->attDate = $attDate;

        return $this;
    }

    /**
     * Get attDate
     *
     * @return \DateTime 
     */
    public function getAttDate()
    {
        return $this->attDate;
    }
 
 /**
  * Get school.
  *
  * @return school.
  */
 function getSchool()
 {
     return $this->school;
 }
 
 /**
  * Set school.
  *
  * @param school the value to set.
  */
 function setSchool($school)
 {
     $this->school = $school;
 }
 
 /**
  * Get class.
  *
  * @return class.
  */
 function getClass()
 {
     return $this->class;
 }
 
 /**
  * Set class.
  *
  * @param class the value to set.
  */
 function setClass($class)
 {
     $this->class = $class;
 }
 
 /**
  * Get schedule.
  *
  * @return schedule.
  */
 function getSchedule()
 {
     return $this->schedule;
 }
 
 /**
  * Set schedule.
  *
  * @param schedule the value to set.
  */
 function setSchedule($schedule)
 {
     $this->schedule = $schedule;
 }
 
 /**
  * Get student.
  *
  * @return student.
  */
 function getStudent()
 {
     return $this->student;
 }
 
 /**
  * Set student.
  *
  * @param student the value to set.
  */
 function setStudent($student)
 {
     $this->student = $student;
 }
 
 /**
  * Get status.
  *
  * @return status.
  */
 function getStatus()
 {
     return $this->status;
 }
 
 /**
  * Set status.
  *
  * @param status the value to set.
  */
 function setStatus($status)
 {
     $this->status = $status;
 }
 
 /**
  * Get notes.
  *
  * @return notes.
  */
 function getNotes()
 {
     return $this->notes;
 }
 
 /**
  * Set notes.
  *
  * @param notes the value to set.
  */
 function setNotes($notes)
 {
     $this->notes = $notes;
 }

 
    /**
     * Get tokens.
     *
     * @return tokens.
     */
    public function getTokens()
    {
        return $this->tokens;
    }
 
    /**
     * Set tokens.
     *
     * @param tokens the value to set.
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
        return $this;
    }
    
    public function __toString()
    {
        return (string) 'attendance: ' . $this->getId();
    }
}
