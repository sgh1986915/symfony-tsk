<?php
namespace TSK\PaymentBundle\Ruler;

use TSK\StudentBundle\Entity\TrainingFamilyMembers;
use TSK\RulerBundle\Ruler\RewardsEngineInterface;
use TSK\RulerBundle\Ruler\RewardsEngine;
use TSK\RulerBundle\Model\Reward;
use Ruler\Context;

class DiscountReward extends RewardsEngine implements RewardsEngineInterface
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

    public function rewardDiscountType($data=null)
    {
        // allow programs to be filtered for program_payment_plan_type 
        // 1 (regular) or 2 (2nd family member)
        $this->context['studentRegistration']->setProgramDiscountTypeFilters($this->reward->getMetaData());
    }
}
