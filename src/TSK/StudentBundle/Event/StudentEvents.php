<?php
namespace TSK\StudentBundle\Event;

final class StudentEvents
{

    /**
     * This event should be thrown anytime a student does
     * something that advances his progress through the 
     * curriculum.  This currently means anytime attendance
     * is recorded for him or anytime he actually receives
     * a promotion
     *
     * @var string
     */
    const STUDENT_PROGRESS          = 'tsk.student.progress';
    const STUDENT_REGISTRATION_PRE  = 'tsk.student.pre_registration';
    const STUDENT_REGISTRATION_POST = 'tsk.student.post_registration';
}
