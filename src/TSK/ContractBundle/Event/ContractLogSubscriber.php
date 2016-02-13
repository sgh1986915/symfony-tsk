<?php
namespace TSK\ContractBundle\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use TSK\ContractBundle\Event\ContractEvent;

class ContractLogSubscriber implements EventSubscriberInterface
{
    private $em;
    public function __construct($em)
    {
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'tsk.contract.cancel' => array('onContractCancel', 0);
        );
    }

    public function onContractCancel(ContractEvent $event)
    {
    }
}
