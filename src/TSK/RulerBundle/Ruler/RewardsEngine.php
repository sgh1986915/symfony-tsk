<?php
namespace TSK\RulerBundle\Ruler;

use Ruler\Context;
use TSK\RulerBundle\Model\Reward;

abstract class RewardsEngine implements RewardsEngineInterface
{
    private $context;
    private $reward;

    public function setContext(Context $context)
    {
        $this->context = $context;
    }

    public function setReward(Reward $reward)
    {
        $this->reward = $reward;
    }
}
