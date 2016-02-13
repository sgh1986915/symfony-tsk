<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use TSK\ContractBundle\Entity\Contract;
use TSK\SchoolBundle\Entity\School;

/**
 * Charge
 *
 * @ORM\Table(name="tsk_charge")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 * @ExclusionPolicy("all")
 */
class Charge
{
    /**
     * @var integer
     *
     * @ORM\Column(name="charge_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Expose
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="legacy_charge_id", type="string")
     */
    private $legacyChargeId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    private $school;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\Account", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_account_id", referencedColumnName="account_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Account")
     */
    protected $account;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\Account", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_deferral_account_id", referencedColumnName="account_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Account")
     */
    private $deferralAccount;

    /**
     * @ORM\OneToMany(targetEntity="ChargePayment", mappedBy="charge", cascade={"all"})
     * @ORM\JoinTable(name="tsk_charge_payment",
     *      joinColumns={@ORM\JoinColumn(name="fk_charge_id", referencedColumnName="charge_id")}
     *  )
     * @Expose
     * */
    protected $chargePayments;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ContractBundle\Entity\Contract", mappedBy="charges")
     */
    private $contracts;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     * @Expose
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="date")
     * @Expose
     */
    private $dueDate;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @Expose
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="misc", type="string", length=255, nullable=true)
     */
    private $misc;

    private $openAmount;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @var datetime $updatedDate
     *
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updatedDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_voided", type="boolean", nullable=true)
     */
    private $isVoided=false;


    public function __construct()
    {
        $this->contracts = new ArrayCollection();
        $this->chargePayments = new ArrayCollection();
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
     * Set amount
     *
     * @param float $amount
     * @return Charge
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Charge
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Charge
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;
    
        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime 
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set misc
     *
     * @param string $misc
     * @return Charge
     */
    public function setMisc($misc)
    {
        $this->misc = $misc;
    
        return $this;
    }

    /**
     * Get misc
     *
     * @return string 
     */
    public function getMisc()
    {
        return $this->misc;
    }
 
 /**
  * Get updatedDate.
  *
  * @return updatedDate.
  */
 public function getUpdatedDate()
 {
     return $this->updatedDate;
 }
 
 /**
  * Set updatedDate.
  *
  * @param updatedDate the value to set.
  */
 public function setUpdatedDate($updatedDate)
 {
     $this->updatedDate = $updatedDate;
     return $this;
 }
 
    /**
     * Get createdBy.
     *
     * @return createdBy.
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
 
    /**
     * Set createdBy.
     *
     * @param createdBy the value to set.
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
        return $this;
    }
 
    /**
     * Get updatedBy.
     *
     * @return updatedBy.
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
 
    /**
     * Set updatedBy.
     *
     * @param updatedBy the value to set.
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }
 
    /**
     * Get dueDate.
     *
     * @return dueDate.
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }
 
    /**
     * Set dueDate.
     *
     * @param dueDate the value to set.
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getContracts()
    {
        return $this->contracts;
    }

    public function setContracts($contracts)
    {
        foreach ($contracts as $idx => $contract) {
            $this->addContract($contract);
        }
    }

    public function addContract(Contract $contract)
    {
        $this->contracts[] = $contract;
    }

    public function removeContract(Contract $contract)
    {
        return $this->contracts->removeElement($contracts);
    }

    public function getChargePayments()
    {
        return $this->chargePayments;
    }

    public function setChargePayments($chargePayments)
    {
        foreach ($chargePayments as $idx => $chargePayment) {
            $this->addChargePayment($chargePayment);
        }
    }

    public function addChargePayment(ChargePayment $chargePayment)
    {
        $this->chargePayments[] = $chargePayment;
    }

    public function removeChargePayment(ChargePayment $chargePayment)
    {
        return $this->chargePayments->removeElement($chargePayment);
    }

    public function setOpenAmount($openAmount)
    {
        $this->openAmount = $openAmount;
    }
    /**
     * getOpenAmount 
     * Determines what the open amount due is on a charge.
     * Checks if charge either has no chargePayment or a
     * chargePayment that is less than the amount due.
     * 
     * @access public
     * @return void
     */
    public function getOpenAmount()
    {
        if ($this->isVoided) {
            return 0;
        }
        $paidTotal = 0;
        if (count($this->getChargePayments())) {
            foreach ($this->getChargePayments() as $cp) {
                $paidTotal += $cp->getAmount();
            }
        } 
        return $this->getAmount() - $paidTotal;
    }

    public function getPaidAmount()
    {
        if ($this->isVoided) {
            return 0;
        }
        $paidTotal = 0;
        if (count($this->getChargePayments())) {
            foreach ($this->getChargePayments() as $cp) {
                $paidTotal += $cp->getAmount();
            }
        } 
        return $paidTotal;
    }

    /**
     * void 
     * Only voids charges that have not had any payment applied
     * 
     * @access public
     * @return void
     */
    public function void()
    {
        $this->setIsVoided(true);
    }

    public function __toString()
    {
        return (string)$this->getDueDate()->format('Y-m-d') . ' $' . $this->getAmount() . ' ' . $this->getOpenAmount();
    }

 
    /**
     * Get incomeType.
     *
     * @return incomeType.
     */
    public function getIncomeType()
    {
        return $this->incomeType;
    }
 
    /**
     * Set incomeType.
     *
     * @param incomeType the value to set.
     */
    public function setIncomeType(IncomeType $incomeType)
    {
        $this->incomeType = $incomeType;
        return $this;
    }

    /**
     * Set isVoided
     *
     * @param boolean $isVoided
     * @return Payment
     */
    public function setIsVoided($isVoided)
    {
        if ($isVoided == true) {
            if ($this->getOpenAmount() == $this->getAmount()) {
                $this->isVoided = true;
            }
        } else {
            $this->isVoided = false;
        }
    
        return $this;
    }

    /**
     * Get isVoided
     *
     * @return boolean 
     */
    public function getIsVoided()
    {
        return $this->isVoided;
    }

 
    /**
     * Get school.
     *
     * @return school.
     */
    public function getSchool()
    {
        return $this->school;
    }
 
    /**
     * Set school.
     *
     * @param school the value to set.
     */
    public function setSchool(School $school)
    {
        $this->school = $school;
        return $this;
    }
 
    /**
     * Get account.
     *
     * @return account.
     */
    public function getAccount()
    {
        return $this->account;
    }
 
    /**
     * Set account.
     *
     * @param account the value to set.
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * prePersist 
     * 
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        if (!$this->getLegacyChargeId()) {
            $this->setLegacyChargeId($now->format('Ymdhis'));
        }
    }

 
    /**
     * Get legacyChargeId.
     *
     * @return legacyChargeId.
     */
    public function getLegacyChargeId()
    {
        return $this->legacyChargeId;
    }
 
    /**
     * Set legacyChargeId.
     *
     * @param legacyChargeId the value to set.
     */
    public function setLegacyChargeId($legacyChargeId)
    {
        $this->legacyChargeId = $legacyChargeId;
        return $this;
    }
 
    /**
     * Get deferralAccount.
     *
     * @return deferralAccount.
     */
    public function getDeferralAccount()
    {
        return $this->deferralAccount;
    }
 
    /**
     * Set deferralAccount.
     *
     * @param deferralAccount the value to set.
     */
    public function setDeferralAccount($deferralAccount)
    {
        $this->deferralAccount = $deferralAccount;
        return $this;
    }
}
