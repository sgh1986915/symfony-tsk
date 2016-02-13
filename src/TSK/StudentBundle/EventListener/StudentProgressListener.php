<?php
namespace TSK\StudentBundle\EventListener;

use TSK\StudentBundle\Event\StudentProgressEvent;
use TSK\RulerBundle\Ruler\RulesEngineInterface;

class StudentProgressListener
{
    private $em;
    private $rankRulesEngine;
    public function __construct($em, RulesEngineInterface $rankRulesEngine)
    {
        $this->em = $em;
        $this->rankRulesEngine = $rankRulesEngine;
    }

    public function onStudentProgress(StudentProgressEvent $event)
    {
        // get student 
        // Run Rank Rules Engine to determine if student is eligible for new rank
        $student = $event->getStudent();
        $this->rankRulesEngine->applyRules('rank', $student);


        //  If student has an existing contract freeze, we set end date of that freeze
        //  to today's date now that an attendance event has occurred.
        $contract = $student->getActiveContract();
        if ($contract) {
            if ($freeze = $contract->getActiveFreeze()) {
                $freeze->setEndDate(new \DateTime());
                $this->em->persist($freeze);
                $this->em->flush();
            }
        }
    }
}
