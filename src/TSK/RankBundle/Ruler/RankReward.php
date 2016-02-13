<?php
namespace TSK\RankBundle\Ruler;
use TSK\RulerBundle\Ruler\RewardsEngineInterface;
use TSK\RulerBundle\Model\Reward;

use Ruler\Context;
/**
 * RankReward 
 * 
 * @package 
 * @version $id$
 * @copyright 2012 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class RankReward implements RewardsEngineInterface
{
    private $em;
    private $context;
    public function __construct($em)
    {
        $this->em = $em;
    }

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function setReward(Reward $reward)
    {
        $this->reward = $reward;
    }


    /**
     * setEligibleRank 
     * Clears all eligible ranks and then sets the given rank
     * as eligible 
     *
     * @param mixed $rankId 
     * @access public
     * @return void
     */
    public function rewardSetEligibleRank($rankId)
    {
        $student = $this->context['student'];
        $rankRepo = $this->em->getRepository('TSK\RankBundle\Entity\Rank');
        $eligibleRank = $rankRepo->find($rankId);
        if ($eligibleRank) {
            $student->clearEligibleRanks();
            $student->setEligibleRank($thisRank);
            $this->em->persist($student);
            $this->em->flush();
            $this->em->getConnection()->commit();
        }
    }
}
