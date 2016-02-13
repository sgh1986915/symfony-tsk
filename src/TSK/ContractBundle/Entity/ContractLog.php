<?php

namespace TSK\ContractBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContractLog
 *
 * @ORM\Table(name="tsk_contract_log")
 * @ORM\Entity
 */
class ContractLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
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
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\ManyToOne(targetEntity="Contract")
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id")
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    protected $contract;

    /**
     * @ORM\ManyToOne(targetEntity="ContractStatus")
     * @ORM\JoinColumn(name="fk_contract_status_id", referencedColumnName="contract_status_id")
     * @Assert\Type(type="TSK\ContractBundle\Entity\ContractStatus")
     */
    protected $contractStatus;

    /**
     * @var string $note
     *
     * @ORM\Column(name="note", type="string", nullable=true)
     */
    protected $note;


    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    private $createdBy;

    /**
      @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;

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
     * Set date
     *
     * @param \DateTime $date
     * @return ContractLog
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
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
 
    /**
     * Get contractStatus.
     *
     * @return contractStatus.
     */
    public function getContractStatus()
    {
        return $this->contractStatus;
    }
 
    /**
     * Set contractStatus.
     *
     * @param contractStatus the value to set.
     */
    public function setContractStatus($contractStatus)
    {
        $this->contractStatus = $contractStatus;
        return $this;
    }
 
    /**
     * Get isActive.
     *
     * @return isActive.
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
 
    /**
     * Set isActive.
     *
     * @param isActive the value to set.
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
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
 
    /**
     * Set contract.
     *
     * @param contract the value to set.
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
        return $this;
    }
 
    /**
     * Get note.
     *
     * @return note.
     */
    public function getNote()
    {
        return $this->note;
    }
 
    /**
     * Set note.
     *
     * @param note the value to set.
     */
    public function setNote($note)
    {
        $this->note = $note;
        return $this;
    }
}
