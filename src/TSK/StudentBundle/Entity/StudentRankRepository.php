<?php

namespace TSK\StudentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use TSK\RankBundle\Entity\RankType;

/**
 * StudentRankRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StudentRankRepository extends EntityRepository
{
    public function getNumberKbStripesEarnedAtCurrentBelt(Student $student)
    {
        $kbRankType = $this->_em->getRepository('TSK\RankBundle\Entity\RankType')->findOneBy(array('name' => 'kickboxing stripe'));
        return $this->getNumberStripesEarnedAtCurrentBelt($student, $kbRankType);
    }

    public function getNumberGrStripesEarnedAtCurrentBelt(Student $student)
    {
        $grRankType = $this->_em->getRepository('TSK\RankBundle\Entity\RankType')->findOneBy(array('name' => 'grappling stripe'));
        return $this->getNumberStripesEarnedAtCurrentBelt($student, $grRankType);
    }

    public function getNumberStripesEarnedAtCurrentBelt(Student $student, RankType $rankType=NULL)
    {
        $clauses = array();
        // TODO: Need to inject org ...
        $beltRankType = $this->_em->getRepository('TSK\RankBundle\Entity\RankType')->findOneBy(array('name' => 'belt'));
        $lastBeltPromoDate = $this->getLastPromotionDate($student, $beltRankType);

        $clauses[] = 'r.student=:student';
        if ($rankType) {
            $clauses[] = 'r.rankType=:rankType';
        } else {
            // This says if we are not specifying a rankType then count any rankType greater than BELT
            // This only works for the TSK organization
            $clauses[] = 'r.rankType > 1';
        } 
        $clauses[] = 'r.awardedDate > :awardedDate';
        $dql = 'SELECT COUNT(r) FROM TSK\StudentBundle\Entity\StudentRank r WHERE ';
        $dql .= join(' AND ', $clauses);
        $query = $this->_em->createQuery($dql);
        $query->setParameter(':student', $student);
        if ($rankType) {
            $query->setParameter(':rankType', $rankType);
        }
        $query->setParameter(':awardedDate', $lastBeltPromoDate);

        try {
            $results = $query->getSingleResult();
            $num = array_pop($results);
        } catch (\Doctrine\Orm\NoResultException $e) {
            $num = 0;
        }
        return $num;
    }
        
    public function getLastKbPromotionDate(Student $student)
    {
        $kbRankType = $this->_em->getRepository('TSK\RankBundle\Entity\RankType')->findOneBy(array('name' => 'kickboxing stripe'));
        return $this->getLastPromotionDate($student, $kbRankType);
    }

    public function getLastGrPromotionDate(Student $student)
    {
        $grRankType = $this->_em->getRepository('TSK\RankBundle\Entity\RankType')->findOneBy(array('name' => 'grappling stripe'));
        return $this->getLastPromotionDate($student, $grRankType);
    }

    public function getLastPromotionDate(Student $student, RankType $rankType=NULL)
    {
        $clauses = array();
        $dql = 'SELECT MAX(r.awardedDate) FROM TSK\StudentBundle\Entity\StudentRank r WHERE ';
        $clauses[] = 'r.student=:student';
        if ($rankType) {
            $clauses[] = 'r.rankType=:rankType';
        } 
        $dql .= join(' AND ', $clauses);
        $query = $this->_em->createQuery($dql);
        $query->setParameter(':student', $student);
        if ($rankType) {
            $query->setParameter(':rankType', $rankType);
        }
        try {
            $results = $query->getSingleResult();
            $result = array_pop($results);
            if (is_null($result)) {
                $promotionDate = new \DateTime('1970-01-01');
            } else {
                $promotionDate = new \DateTime($result);
            }
        } catch (\Doctrine\Orm\NoResultException $e) {
            $promotionDate = null;
        }
        return $promotionDate;

    }
}
