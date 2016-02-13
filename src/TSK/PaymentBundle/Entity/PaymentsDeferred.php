<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\ContractBundle\Entity\Contract;

/**
 * PaymentsDeferred
 *
 * @ORM\Table(name="tsk_payments_deferred")
 * @ORM\Entity
 */
class PaymentsDeferred
{
    /**
     * @var integer
     *
     * @ORM\Column(name="payments_deferred_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Payment")
     * @ORM\JoinColumn(name="fk_payment_id", referencedColumnName="payment_id")
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Payment")
     */
    private $payment;

    /**
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract")
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id")
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
     * @ORM\Column(name="date_realized", type="date")
     */
    private $dateRealized;


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
     * @return PaymentsDeferred
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
     * Set date_realized
     *
     * @param \DateTime $dateRealized
     * @return PaymentsDeferred
     */
    public function setDateRealized($dateRealized)
    {
        $this->dateRealized = $dateRealized;
    
        return $this;
    }

    /**
     * Get date_realized
     *
     * @return \DateTime 
     */
    public function getDateRealized()
    {
        return $this->dateRealized;
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
 }
}
