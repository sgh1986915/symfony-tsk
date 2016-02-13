<?php

namespace TSK\ContractBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use TSK\ContractBundle\Entity\Contract;

class ContractEvent extends Event {
    private $contract;

    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

 
    /**
     * Get contract.
     *
     * @return contract.
     */
    public function getContract()
    {
        return $this->contract;
    }
}
