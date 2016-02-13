<?php
namespace TSK\StudentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TSK\StudentBundle\Form\Model\StudentRegistration;
use TSK\StudentBundle\Entity\Student;

class StudentPostRegistrationEvent extends Event
{
    protected $student;
    protected $studentRegistration;

    public function __construct(Student $student, StudentRegistration $studentRegistration)
    {
        $this->student = $student;
        $this->studentRegistration = $studentRegistration;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function getStudentRegistration()
    {
        return $this->studentRegistration;
    }
}
