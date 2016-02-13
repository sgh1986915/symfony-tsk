<?php
namespace TSK\StudentBundle\EventListener;

use TSK\StudentBundle\Event\StudentPostRegistrationEvent;
use TSK\StudentBundle\Form\Model\StudentRegistration;
use TSK\StudentBundle\Entity\StudentRank;
use TSK\StudentBundle\Entity\TrainingFamilyMembers;
use TSK\RulerBundle\Ruler\RulesEngineInterface;

class StudentRegistrationListener
{
    private $em;
    private $programRulesEngine;
    public function __construct($em, RulesEngineInterface $programRulesEngine)
    {
        $this->em = $em;
        $this->programRulesEngine = $programRulesEngine;
    }

    public function onPostRegistration(StudentPostRegistrationEvent $event)
    {
        $student = $event->getStudent();
        $studentRegistration = $event->getStudentRegistration();
        $this->initializeStudentBelt($student);
        // determine if student needs to be added to training family members table
        $this->programRulesEngine->applyRules('program.post', $studentRegistration);

        // create initial contract doc
        $contract = $studentRegistration->getContract();
        $doc = $contract->renderContractVersion();
        $this->em->persist($doc);
        $this->em->flush();
    }

    public function onPreRegistration(StudentPreRegistrationEvent $event)
    {
        $studentRegistration = $event->getStudentRegistration();
    }


    private function initializeStudentBelt($student)
    {
        $studentRank = new StudentRank();
        $studentRank->setStudent($student);
        $studentRank->setRank($this->em->getRepository('TSK\RankBundle\Entity\Rank')->find(1)); // WHITE BELT  -- config options?
        $studentRank->setRankType($this->em->getRepository('TSK\RankBundle\Entity\RankType')->find(1)); // BELT TYPE  -- config options?
        $this->em->persist($studentRank);
        $this->em->flush();
    }
}
