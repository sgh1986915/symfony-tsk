<?php

namespace TSK\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\ClassBundle\Entity\ClassType;

/**
 * StudentCreditLog
 *
 * @ORM\Table(name="tsk_student_credit_log", indexes={@ORM\Index(name="student_class_type", columns={"fk_student_id", "fk_class_type_id"})})
 * @ORM\Entity(repositoryClass="TSK\StudentBundle\Entity\StudentCreditLogRepository")

 */
class StudentCreditLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="student_credit_log_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student")
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id")
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ClassBundle\Entity\ClassType")
     * @ORM\JoinColumn(name="fk_class_type_id", referencedColumnName="class_type_id")
     * @Assert\Type(type="TSK\ClassBundle\Entity\ClassType")
     */
    private $classType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ScheduleBundle\Entity\ScheduleAttendance")
     * @ORM\JoinColumn(name="fk_schedule_attendance_id", referencedColumnName="schedule_attendance_id", onDelete="CASCADE")
     * @Assert\Type(type="TSK\ScheduleBundle\Entity\ScheduleAttendance")
     */
    private $attendance;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=true)
     */
    private $value;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_earned", type="date")
     * @Gedmo\Timestampable(on="create")
     */
    private $dateEarned;

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
     * Set value
     *
     * @param integer $value
     * @return StudentCreditLog
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set dateEarned
     *
     * @param \DateTime $dateEarned
     * @return StudentCreditLog
     */
    public function setDateEarned($dateEarned)
    {
        $this->dateEarned = $dateEarned;

        return $this;
    }

    /**
     * Get dateEarned
     *
     * @return \DateTime 
     */
    public function getDateEarned()
    {
        return $this->dateEarned;
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
    public function setStudent(Student $student)
    {
        $this->student = $student;
        return $this;
    }
 
    /**
     * Get classType.
     *
     * @return classType.
     */
    public function getClassType()
    {
        return $this->classType;
    }
 
    /**
     * Set classType.
     *
     * @param classType the value to set.
     */
    public function setClassType(ClassType $classType)
    {
        $this->classType = $classType;
        return $this;
    }
 
    /**
     * Get attendance.
     *
     * @return attendance.
     */
    public function getAttendance()
    {
        return $this->attendance;
    }
 
    /**
     * Set attendance.
     *
     * @param attendance the value to set.
     */
    public function setAttendance($attendance)
    {
        $this->attendance = $attendance;
        return $this;
    }
}
