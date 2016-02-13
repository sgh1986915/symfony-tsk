<?php

namespace TSK\RecurringPaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\BilleeBundle\Entity\BilleePaymentMethod;
use TSK\ContractBundle\Entity\Contract;

/**
 * RecurringPaymentInstruction
 *
 * @ORM\Table(name="tsk_recurring_payment_instruction")
 * @ORM\Entity
 */
class RecurringPaymentInstruction
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rpi_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\BilleeBundle\Entity\BilleePaymentMethod", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_bpm_id", referencedColumnName="billee_payment_method_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\BilleeBundle\Entity\BilleePaymentMethod")
     */
    private $billeePaymentMethod;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    private $contract;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="run_date", type="date")
     */
    private $runDate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status;

    /**
     * @var float
     *
     * @ORM\Column(name="amount_collected", type="float", nullable=true)
     */
    private $amountCollected;

    /**
     * @var string
     *
     * @ORM\Column(name="status_text", type="text", nullable=true)
     */
    private $statusText;

    /**
     * @var integer
     *
     * @ORM\Column(name="attempts", type="smallint", nullable=true)
     */
    private $attempts;

    /**
     * created 
     * 
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $created;

    /**
     * @var string createdBy 
     * 
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    private $createdBy;

    /**
     * updated 
     * 
     * @Gedmo\Timestampable(on="update")
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updated;

    /**
     * updatedBy 
     * 
     * @Gedmo\Blameable(on="update")
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    private $updatedBy;


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
     * @return RecurringPaymentInstruction
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
     * Set status
     *
     * @param string $status
     * @return RecurringPaymentInstruction
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set amount_collected
     *
     * @param float $amountCollected
     * @return RecurringPaymentInstruction
     */
    public function setAmountCollected($amountCollected)
    {
        $this->amount_collected = $amountCollected;
    
        return $this;
    }

    /**
     * Get amount_collected
     *
     * @return float 
     */
    public function getAmountCollected()
    {
        return $this->amount_collected;
    }

    /**
     * Set status_text
     *
     * @param string $statusText
     * @return RecurringPaymentInstruction
     */
    public function setStatusText($statusText)
    {
        $this->status_text = $statusText;
    
        return $this;
    }

    /**
     * Get status_text
     *
     * @return string 
     */
    public function getStatusText()
    {
        return $this->status_text;
    }

    /**
     * Set attempts
     *
     * @param integer $attempts
     * @return RecurringPaymentInstruction
     */
    public function setAttempts($attempts)
    {
        $this->attempts = $attempts;
    
        return $this;
    }

    /**
     * Get attempts
     *
     * @return integer 
     */
    public function getAttempts()
    {
        return $this->attempts;
    }
 
    /**
     * Get billeePaymentMethod.
     *
     * @return billeePaymentMethod.
     */
    public function getBilleePaymentMethod()
    {
        return $this->billeePaymentMethod;
    }
 
    /**
     * Set billeePaymentMethod.
     *
     * @param billeePaymentMethod the value to set.
     */
    public function setBilleePaymentMethod(BilleePaymentMethod $billeePaymentMethod)
    {
        $this->billeePaymentMethod = $billeePaymentMethod;
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
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        return $this;
    }
 
    /**
     * Get runDate.
     *
     * @return runDate.
     */
    public function getRunDate()
    {
        return $this->runDate;
    }
 
    /**
     * Set runDate.
     *
     * @param runDate the value to set.
     */
    public function setRunDate($runDate)
    {
        $this->runDate = $runDate;
        return $this;
    }
 
    /**
     * Get created.
     *
     * @return created.
     */
    public function getCreated()
    {
        return $this->created;
    }
 
    /**
     * Set created.
     *
     * @param created the value to set.
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
     * Get updated.
     *
     * @return updated.
     */
    public function getUpdated()
    {
        return $this->updated;
    }
 
    /**
     * Set updated.
     *
     * @param updated the value to set.
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
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
}
