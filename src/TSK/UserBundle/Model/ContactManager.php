<?php
namespace TSK\UserBundle\Model;
use FOS\UserBundle\Util\CanonicalizerInterface;

class ContactManager
{
    protected $emailCanonicalizer;
    protected $phoneCanonicalizer;
    protected $postalCodeCanonicalizer;

    public function __construct(CanonicalizerInterface $emailCanonicalizer, CanonicalizerInterface $phoneCanonicalizer, CanonicalizerInterface $postalCodeCanonicalizer)
    {
        $this->emailCanonicalizer = $emailCanonicalizer;
        $this->phoneCanonicalizer = $phoneCanonicalizer;
        $this->postalCodeCanonicalizer = $postalCodeCanonicalizer;
    }

    public function createContact()
    {
        $class = $this->getClass();
        $contact = new $class;

        return $contact;
    }

    public function canonicalizeEmail($email)
    {
        return $this->emailCanonicalizer->canonicalize($email);
    }

    public function canonicalizePhone($phone)
    {
        return $this->phoneCanonicalizer->canonicalize($phone);
    }

    public function canonicalizePostalCode($postalCode)
    {
        return $this->postalCodeCanonicalizer->canonicalize($postalCode);
    }

    public function updateCanonicalFields(ContactInterface $contact)
    {
        $contact->setEmailCanonical($this->canonicalizeEmail($contact->getEmail()));
        $contact->setPostalCodeCanonical($this->canonicalizePostalCode($contact->getPostalCode()));
        $contact->setPhoneCanonical($this->canonicalizePhone($contact->getPhone()));
        $contact->setMobileCanonical($this->canonicalizePhone($contact->getMobile()));
    }
}
