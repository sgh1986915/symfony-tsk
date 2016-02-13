<?php
namespace TSK\RulerBundle\Ruler;

class RewardsEngineChain
{
    private $rewardsEngines;

    public function __construct()
    {
        $this->rewardsEngines = array();
    }

    public function addRewardsEngine(RewardsEngineInterface $rewardsEngine, $alias)
    {
        $this->rewardsEngines[$alias] = $rewardsEngine;
    }

    public function getRewardsEngine($alias)
    {
        if (array_key_exists($alias, $this->rewardsEngines)) {
            return $this->rewardsEngines[$alias];
        }
    }

    public function getRewardsEngines()
    {
        return $this->rewardsEngines;
    }
}
