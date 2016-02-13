<?php

namespace TSK\UserBundle\Doctrine;

use TSK\UserBundle\Model\ContactInterface;
use TSK\UserBundle\Model\ContactManager as BaseContactManager;
use FOS\UserBundle\Util\CanonicalizerInterface;

class ContactManager extends BaseContactManager
{
    public function __construct(CanonicalizerInterface $emailCanonicalizer, CanonicalizerInterface $phoneCanonicalizer, CanonicalizerInterface $postalCodeCanonicalizer, $om, $class)
    {
        parent::__construct($emailCanonicalizer, $phoneCanonicalizer, $postalCodeCanonicalizer);

        $this->objectManager = $om;
        $this->repository = $om->getRepository($class);
        $metadata = $om->getClassMetaData($class);
        $this->class = $metadata->getName();
    }

    public function getClass()
    {
        return $this->class;
    }

    public function deleteContact(ContactInterface $contact)
    {
        $this->objectManager->remove($contact);
        $this->objectManager->flush();
    }

    public function findContactBy(array $criteria)
    {
        return $this->repository->findOneBy($criteria);
    }

    public function findContacts()
    {
        return $this->repository->findAll();
    } public function reloadContact($contact) {
        $this->objectManager->refresh($contact);
    }

    public function updateContact(ContactInterface $contact, $andFlush = true)
    {
        $this->updateCanonicalFields($contact);
        $this->objectManager->persist($contact);
        if ($andFlush) {
            $this->objectManager->flush();
        }
    }
}
