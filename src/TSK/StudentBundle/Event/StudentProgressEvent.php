<?php
namespace TSK\StudentBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TSK\StudentBundle\Entity\Student;

class StudentProgressEvent extends Event
{
    protected $student;

    public function __construct(Student $student)
    {
        $this->student = $student;
    }

    public function getStudent()
    {
        return $this->student;
    }
}
