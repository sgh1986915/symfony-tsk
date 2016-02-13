<?php

namespace TSK\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use TSK\ContractBundle\Entity\ContractTemplate;

/**
 * MembershipType
 *
 * @ORM\Table(name="tsk_ref_membership_type")
 * @ORM\Entity
 */
class MembershipType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="membership_type_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\ContractTemplate", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contract_template_id", referencedColumnName="contract_template_id", nullable=true)
     * @Assert\Type(type="TSK\ContractBundle\Entity\ContractTemplate")
     */
    private $contractTemplate;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="short_desc", type="string", length=100, nullable=true)
     */
    private $shortDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="long_desc", type="string", length=255, nullable=true)
     */
    private $longDescription;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ContractBundle\Entity\ContractTemplate")
     * @ORM\JoinTable(name="tsk_membership_type_has_contract_template",
     *      joinColumns={@ORM\JoinColumn(name="fk_membership_type_id", referencedColumnName="membership_type_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_contract_template_id", referencedColumnName="contract_template_id", onDelete="CASCADE")}
     *      )
     */




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

    /**
     * Set name
     *
     * @param string $name
     * @return MembershipType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * Get shortDescription.
     *
     * @return shortDescription.
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }
 
    /**
     * Set shortDescription.
     *
     * @param shortDescription the value to set.
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }
 
    /**
     * Get longDescription.
     *
     * @return longDescription.
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }
 
    /**
     * Set longDescription.
     *
     * @param longDescription the value to set.
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
        return $this;
    }
 
    /**
     * Get organization.
     *
     * @return organization.
     */
    public function getOrganization()
    {
        return $this->organization;
    }
 
    /**
     * Set organization.
     *
     * @param organization the value to set.
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
 
    /**
     * Set contractTemplate.
     *
     * @param contractTemplate the value to set.
     */
    public function setContractTemplate(ContractTemplate $contractTemplate)
    {
        $this->contractTemplate = $contractTemplate;
    }

    public function getContractTemplate() 
    {
        return $this->contractTemplate;
    }
}
