<?php
namespace TSK\StudentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TSK\StudentBundle\Form\Model\StudentRegistration;

class StudentPreRegistrationEvent extends Event
{
    protected $studentRegistration;

    public function __construct(StudentRegistration $studentRegistration)
    {
        $this->studentRegistration = $studentRegistration;
    }

    public function getStudentRegistration()
    {
        return $this->studentRegistration;
    }
}
