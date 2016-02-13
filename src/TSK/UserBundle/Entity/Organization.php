<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Organization
 *
 * @ORM\Table(name="tsk_organization")
 * @ORM\Entity(repositoryClass="TSK\UserBundle\Entity\OrganizationRepository")
 */
class Organization
{
    /**
     * @var integer
     *
     * @ORM\Column(name="org_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\UserBundle\Entity\Contact")
     * @ORM\JoinTable(name="tsk_organization_contact",
     *      joinColumns={@ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id")}
     *      )
     */
    protected $contacts;

//     /**
//      *
//      * @ORM\ManyToOne(targetEntity="Contact", cascade={"persist", "remove"})
//      * @ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id", nullable=true, onDelete="CASCADE")
//      * @Assert\Type(type="TSK\UserBundle\Entity\Contact")
//      */
//     protected $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    protected $title;

    /**
     * Set title
     *
     * @param string $title
     * @return Organization
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * setId 
     * 
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

/*
    public function getContact()
    {
        return $this->contact;
    }

    public function setContact(Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }
*/

    public function getContacts()
    {
        return $this->contacts;
    }
 
    /**
     * Set contact.
     *
     * @param contact the value to set.
     */
    public function setContacts($contacts)
    {
        foreach ($contacts as $idx => $contact) {
            $this->addContact($contact);
        }
    }

    public function addContact(Contact $contact) 
    {
        $this->contacts[] = $contact;
    }

    public function removeContact(Contact $contact)
    {
        return $this->contact->removeElement($contact);
    }


/*
    public function getCorporation()
    {
        return $this->corporation;
    }

    public function setCorporation(\TSK\UserBundle\Entity\Corporation $corporation)
    {
        $this->corporation = $corporation;
        return $this;
    }
*/


    public function __toString()
    {
        return $this->title;
    }
}
