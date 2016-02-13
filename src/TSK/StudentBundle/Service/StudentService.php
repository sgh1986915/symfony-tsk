<?php

namespace TSK\StudentBundle\Service;

class StudentService
{
    private $em;

    public function __construct($em)
    {
        $this->em = $em;
    }

    public function getLastPromotionDate($rankType=NULL)
    {
        $clauses = array();
        $dql = 'SELECT MAX(r.awardedDate) FROM TSK\StudentBundle\Entity\StudentRank r WHERE ';
        $clauses[] = 'student=:student';
        if ($rankType) {
            $clauses[] = 'rankType=:rankType';
        } 
        $dql .= join(' AND ', $clauses);
        $query = $this->em->createQuery($dql);
        $query->setParameter(':student', $this);
        if ($rankType) {
            $query->setParameter(':rankType', $rankType);
        }
        return $query->getResult();
    }

    public function getCreditsEarnedSinceLastPromotion($classType=NULL, $rankType=NULL) 
    {
        $lastPromotionDate = $this->getLastPromotionDate($rankType);
        $dql = 'SELECT SUM(c.value) FROM TSK\StudentBundle\Entity\StudentCreditLog c WHERE student=:student AND classType=:classType AND dateEarned > :lastPromotionDate';
        $query = $this->em->createQuery($dql);
        $query->setParameter(':student', $this);
        $query->setParameter(':classType', $classType);
        $query->setParameter(':lastPromotionDate', $lastPromotionDate);
        return $query->getResult();
    }

    public function getKbCreditsEarnedSinceLastKbPromotion()
    {
        return $this->getCreditsEarnedSinceLastPromotion($kbClassType, $kbRankType);
    }

    public function getGrCreditsEarnedSinceLastGrPromotion()
    {
        return $this->getCreditsEarnedSinceLastPromotion($grClassType, $grRankType);
    }

    public function getNumStripesEarnedAtCurrentBelt($stripeType)
    {
        $lastBeltPromoDate = $this->getLastPromotionDate('belt');
        $dql = 'SELECT sum(r) FROM TSK\StudentBundle\Entity\StudentRank r WHERE student=:student AND rankType=:rankType and awardedDate > :lastBeltPromoDate';
        $query = $this->em->createQuery($dql);
        $query->setParameter(':student', $this);
        $query->setParameter(':rankType', $rankType);
        $query->setParameter(':lastBeltPromoDate', $lastBeltPromoDate);
        return $query->getResult();
    }
}

