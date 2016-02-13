<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Income
 *
 * @ORM\Table(name="tsk_income")
 * @ORM\Entity
 */
class Income
{
    /**
     * @var integer
     *
     * @ORM\Column(name="income_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Payment", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_payment_id", referencedColumnName="payment_id", nullable=false, unique=false)
     * @Assert\Type(type="\TSK\PaymentBundle\Entity\Payment")
     */
    private $payment;

    /**
     *
     * @ORM\ManyToOne(targetEntity="IncomeType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_income_type_id", referencedColumnName="income_type_id", nullable=false, unique=false)
     * @Assert\Type(type="\TSK\PaymentBundle\Entity\IncomeType")
     */
    private $incomeType;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="maturity_date", type="date")
     */
    private $maturityDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;


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
     * @return Income
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
     * Set maturity_date
     *
     * @param \DateTime $maturityDate
     * @return Income
     */
    public function setMaturityDate($maturityDate)
    {
        $this->maturityDate = $maturityDate;
    
        return $this;
    }

    /**
     * Get maturity_date
     *
     * @return \DateTime 
     */
    public function getMaturityDate()
    {
        return $this->maturityDate;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Income
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
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
    public function setPayment($payment)
    {
        $this->payment = $payment;
        return $this;
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
    public function setIncomeType($incomeType)
    {
        $this->incomeType = $incomeType;
        return $this;
    }
}
