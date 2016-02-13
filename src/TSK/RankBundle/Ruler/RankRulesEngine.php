<?php
namespace TSK\RankBundle\Ruler;

use Ruler\Context;
use TSK\RulerBundle\Ruler\RulesEngine;
use TSK\RulerBundle\Model\RuleGroup;
use TSK\RulerBundle\Ruler\RulesEngineInterface;
use TSK\StudentBundle\Entity\Student;

class RankRulesEngine extends RulesEngine implements RulesEngineInterface
{
    protected $em;
    protected $debug = true;

    public function __construct($em, $rewardsEngine, $debug=false)
    {
        parent::__construct($em, $rewardsEngine, $debug);
        $this->em = $em;
    }

    public function buildContext($studentRegistration)
    {
        // Build context ...
        $srRepo = $this->em->getRepository('TSKStudentBundle:StudentRank');
        $studentCreditRepo = $this->em->getRepository('TSKStudentBundle:StudentCreditLog');
        $student = $studentRegistration->getStudent();
        
        $ctx = new Context(array(
            'student' => $student,
            'currentRank' => $student->getRank()->getId(),
            'numberStripesEarnedAtCurrentBelt' => $srRepo->getNumberStripesEarnedAtCurrentBelt($student),
            'numberKbStripesEarnedAtCurrentBelt' => $srRepo->getNumberKbStripesEarnedAtCurrentBelt($student),
            'numberGrStripesEarnedAtCurrentBelt' => $srRepo->getNumberGrStripesEarnedAtCurrentBelt($student),
            'lastPromotionDate' => $srRepo->getLastPromotionDate($student),
            'lastKbPromotionDate' => $srRepo->getLastKbPromotionDate($student),
            'lastGrPromotionDate' => $srRepo->getLastGrPromotionDate($student),
            'creditsEarnedSinceLastPromotion' => $studentCreditRepo->getCreditsEarnedSinceLastPromotion($student),
            'kbCreditsEarnedSinceLastKbPromotion' => $studentCreditRepo->getKbCreditsEarnedSinceLastKbPromotion($student),
            'grCreditsEarnedSinceLastGrPromotion' => $studentCreditRepo->getGrCreditsEarnedSinceLastGrPromotion($student),
        ));
        return $ctx;
    }
}
