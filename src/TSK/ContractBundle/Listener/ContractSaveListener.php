<?php

namespace TSK\ContractBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ContractBundle\Entity\Contract;
use TSK\ContractBundle\Entity\ContractSchool;

class ContractSaveListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
        );
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $this->handleEvent($args);
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        // $this->handleEvent($args);
        $em = $args->getEntityManager();
        $contract = $args->getEntity();
        if ($contract instanceof Contract) {
            // save contract doc
            $doc = $contract->renderContractVersion();
            $contract->addDoc($doc);
            $em->persist($doc);
            $em->flush();
        }
    }

    private function handleEvent(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $contract = $args->getEntity();
        if ($contract instanceof Contract) {
            // set contract school
            $cs = new ContractSchool();
            $cs->setSchool($contract->getSchool());
            $cs->setContract($contract);
            $em->persist($cs);

            // Save contract doc
            // We cannot do a renderContractVersion here because at this point, 
            // the contract is saved, but its students are not and we need the
            // students in order to create the contract doc
            // $doc = $contract->renderContractVersion();
            // $contract->addDoc($doc);
            // $em->persist($doc);
            // $em->flush();
        }
    }
}
