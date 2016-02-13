<?php

namespace TSK\PaymentBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use TSK\SchoolBundle\Entity\School;

/**
 * Journal
 *
 * @ORM\Table(name="tsk_journal")
 * @ORM\Entity
 */
class Journal
{
    /**
     * @var integer
     *
     * @ORM\Column(name="journal_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    private $school;

    /**
     * @var float
     *
     * @ORM\Column(name="voucher_id", type="integer")
     */
    private $voucherId=0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="journal_date", type="datetime", nullable=true)
     * @Gedmo\Timestampable(on="create")
     */
    private $journalDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="posted_date", type="datetime", nullable=true)
     */
    private $postedDate;

    /**
     * @var float
     *
     * @ORM\Column(name="debit_amount", type="float")
     */
    private $debitAmount;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Account", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_debit_account_id", referencedColumnName="account_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Payment")
     */
    private $debitAccount;

    /**
     * @var float
     *
     * @ORM\Column(name="credit_amount", type="float")
     */
    private $creditAmount;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Account", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_credit_account_id", referencedColumnName="account_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Account")
     */
    private $creditAccount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Charge", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_charge_id", referencedColumnName="charge_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Charge")
     */
    private $charge;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Payment", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_payment_id", referencedColumnName="payment_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Payment")
     */
    private $payment;

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
     * Set journalDate
     *
     * @param \DateTime $journalDate
     * @return Journal
     */
    public function setJournalDate($journalDate)
    {
        $this->journalDate = $journalDate;

        return $this;
    }

    /**
     * Get journalDate
     *
     * @return \DateTime 
     */
    public function getJournalDate()
    {
        return $this->journalDate;
    }

    /**
     * Set postedDate
     *
     * @param \DateTime $postedDate
     * @return Journal
     */
    public function setPostedDate($postedDate)
    {
        $this->postedDate = $postedDate;

        return $this;
    }

    /**
     * Get postedDate
     *
     * @return \DateTime 
     */
    public function getPostedDate()
    {
        return $this->postedDate;
    }

    /**
     * Set debitAmount
     *
     * @param float $debitAmount
     * @return Journal
     */
    public function setDebitAmount($debitAmount)
    {
        $this->debitAmount = $debitAmount;

        return $this;
    }

    /**
     * Get debitAmount
     *
     * @return float 
     */
    public function getDebitAmount()
    {
        return $this->debitAmount;
    }

    /**
     * Set creditAmount
     *
     * @param float $creditAmount
     * @return Journal
     */
    public function setCreditAmount($creditAmount)
    {
        $this->creditAmount = $creditAmount;

        return $this;
    }

    /**
     * Get creditAmount
     *
     * @return float 
     */
    public function getCreditAmount()
    {
        return $this->creditAmount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Journal
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
     * Get debitAccount.
     *
     * @return debitAccount.
     */
    public function getDebitAccount()
    {
        return $this->debitAccount;
    }
 
    /**
     * Set debitAccount.
     *
     * @param debitAccount the value to set.
     */
    public function setDebitAccount($debitAccount)
    {
        $this->debitAccount = $debitAccount;
        return $this;
    }
 
    /**
     * Get creditAccount.
     *
     * @return creditAccount.
     */
    public function getCreditAccount()
    {
        return $this->creditAccount;
    }
 
    /**
     * Set creditAccount.
     *
     * @param creditAccount the value to set.
     */
    public function setCreditAccount($creditAccount)
    {
        $this->creditAccount = $creditAccount;
        return $this;
    }
 
    /**
     * Get charge.
     *
     * @return charge.
     */
    public function getCharge()
    {
        return $this->charge;
    }
 
    /**
     * Set charge.
     *
     * @param charge the value to set.
     */
    public function setCharge(Charge $charge)
    {
        $this->charge = $charge;
        return $this;
    }
 
    /**
     * Get payment.
     *
     * @return payment.
     */
    public function getPayment()
    {
        return $this->payment;
    }
 
    /**
     * Set payment.
     *
     * @param payment the value to set.
     */
    public function setPayment(Payment $payment)
    {
        $this->payment = $payment;
        return $this;
    }
 
    /**
     * Get voucherId.
     *
     * @return voucherId.
     */
    public function getVoucherId()
    {
        return $this->voucherId;
    }
 
    /**
     * Set voucherId.
     *
     * @param voucherId the value to set.
     */
    public function setVoucherId($voucherId)
    {
        $this->voucherId = $voucherId;
        return $this;
    }
}
