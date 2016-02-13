<?php
namespace TSK\ProgramBundle\Ruler;

use Ruler\Context;
use Doctrine\ORM\EntityManager;
use TSK\RulerBundle\Ruler\RulesEngine;
use TSK\RulerBundle\Model\RuleGroup;
use TSK\RulerBundle\Ruler\RulesEngineInterface;
use TSK\RulerBundle\Ruler\RewardsEngineInterface;
use TSK\StudentBundle\Form\Model\StudentRegistration;

class ProgramRulesEngine extends RulesEngine implements RulesEngineInterface
{
    public function buildContext($studentRegistration)
    {
        if ($studentRegistration->getStudent()) {
            $studentNumContracts = $studentRegistration->getStudent()->getContracts()->count();
        } else {
            // Check if this contact already has a student record
            $studentRepo = $this->em->getRepository('TSK\StudentBundle\Entity\Student');
            $student = $studentRepo->findOneBy(array('contact' => $studentRegistration->getStudentContact()));
            if ($student) {
                $studentNumContracts = $student->getContracts()->count();
            } else {
                $studentNumContracts = 0;
            }
        }
        if ($studentRegistration->getProgram()) {
            $programId = $studentRegistration->getProgram()->getId();
            $membershipTypeId = $studentRegistration->getProgram()->getMembershipType()->getId();
        } else {
            $programId = 0;
            $membershipTypeId = 0;
        }

        // Build context ...
        $ctx = new Context(array(
            'programId' => $programId,
            'programMembershipTypeId' => $membershipTypeId,
            'studentRegistration' => $studentRegistration,
            'student.numContracts' => $studentNumContracts
        ));
        return $ctx;
    }
}
