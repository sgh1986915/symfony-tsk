<?php

namespace TSK\InstructorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Instructor
 *
 * @ORM\Table(name="tsk_instructor")
 * @ORM\Entity
 */
class Instructor
{
    /**
     * @var integer
     *
     * @ORM\Column(name="instructor_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id", nullable=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Contact")
     */
    protected $contact;


    /**
     * @var string
     *
     * @ORM\Column(name="qualifications", type="string", length=255)
     */
    private $qualifications;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ScheduleBundle\Entity\ScheduleEntity", mappedBy="instructors")
     */
    private $schedules;

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
     * Set qualifications
     *
     * @param string $qualifications
     * @return Instructor
     */
    public function setQualifications($qualifications)
    {
        $this->qualifications = $qualifications;
    
        return $this;
    }

    /**
     * Get qualifications
     *
     * @return string 
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact(\TSK\UserBundle\Entity\Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function __toString()
    {
        if ($this->getContact()) {
            return $this->getContact()->getFirstName() .' ' . $this->getContact()->getLastName();
        } else {
            return 'new instructor';
        }
    }
}
