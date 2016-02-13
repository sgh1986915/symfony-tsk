<?php

namespace TSK\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\StudentBundle\Entity\Student;

/**
 * TrainingFamilyMembers
 *
 * @ORM\Table(name="tsk_training_family_members", uniqueConstraints={@ORM\UniqueConstraint(name="tfm_uniq", columns={"fk_primary_student_id", "fk_student_id"})})
 * @ORM\Entity(repositoryClass="TSK\StudentBundle\Entity\TrainingFamilyMembersRepository")
 */
class TrainingFamilyMembers
{
    /**
     * @var integer
     *
     * @ORM\Column(name="training_family_member_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student")
     * @ORM\JoinColumn(name="fk_primary_student_id", referencedColumnName="student_id", nullable=false)
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $primaryStudent;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student")
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id", nullable=false)
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_added", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $dateAdded;

    /**
     * @var int
     *
     * @ORM\Column(name="ordinal_position", type="integer", nullable=false)
     */
    private $ordinalPosition;

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
     * Set dateAdded
     *
     * @param \DateTime $dateAdded
     * @return TrainingFamilyMembers
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded
     *
     * @return \DateTime 
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }
 
    /**
     * Get primaryStudent.
     *
     * @return primaryStudent.
     */
    public function getPrimaryStudent()
    {
        return $this->primaryStudent;
    }
 
    /**
     * Set primaryStudent.
     *
     * @param primaryStudent the value to set.
     */
    public function setPrimaryStudent(Student $primaryStudent)
    {
        $this->primaryStudent = $primaryStudent;
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
    public function setStudent(Student $student)
    {
        $this->student = $student;
        return $this;
    }
 
    /**
     * Get ordinalPosition.
     *
     * @return ordinalPosition.
     */
    public function getOrdinalPosition()
    {
        return $this->ordinalPosition;
    }
 
    /**
     * Set ordinalPosition.
     *
     * @param ordinalPosition the value to set.
     */
    public function setOrdinalPosition($ordinalPosition)
    {
        $this->ordinalPosition = $ordinalPosition;
        return $this;
    }

    public function __toString()
    {
        return (string)$this->getPrimaryStudent()->getContact();
    }
}
