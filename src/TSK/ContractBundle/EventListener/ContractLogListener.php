<?php

namespace TSK\ContractBundle\EventListener;

use Symfony\Component\EventDispatcher\Event;
use TSK\ContractBundle\Event\ContractEvent;
use TSK\ContractBundle\Entity\ContractLog;

class ContractLogListener
{
    private $em;
    private $logger;

    public function __construct($em, $logger)
    {
        $this->em = $em;
        $this->logger = $logger;
    }

    public function onCancelAction(ContractEvent $event)
    {
        // set contract to inactive
        $contract = $event->getContract();
        $contract->setIsActive(0);

        // void all un-paid charges on contract?
        foreach ($contract->getCharges() as $charge) {
            $charge->void();
            $this->em->persist($charge);
        }

        // write to contract log
        $org = $contract->getSchool()->getContact()->getOrganization();
        $contractStatusCancelled = $this->em->getRepository('TSK\ContractBundle\Entity\ContractStatus')->findOneBy(array('name' => 'cancelled', 'organization' => $org));
        $contractLog = new ContractLog();
        $contractLog->setIsActive($contract->getIsActive());
        $contractLog->setContract($contract);
        $contractLog->setContractStatus($contractStatusCancelled);

        // set student to inactive
        $studentStatusCancelled = $this->em->getRepository('TSK\StudentBundle\Entity\StudentStatus')->findOneBy(array('name' => 'cancelled', 'organization' => $org));
        foreach ($contract->getStudents() as $student) {
            $student->setStudentStatus($studentStatusCancelled);
            $this->em->persist($student);
        }
        $this->em->persist($contract);
        $this->em->persist($contractLog);
        $this->em->flush();
    }
}
