<?php
namespace TSK\RulerBundle\Ruler;

use Ruler\Context;
use TSK\RulerBundle\Model\Reward;

interface RewardsEngineInterface
{
    public function setContext(Context $context);
    public function setReward(Reward $reward);
}
