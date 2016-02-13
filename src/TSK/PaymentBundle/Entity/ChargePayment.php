<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ChargePayment
 *
 * @ORM\Table(name="tsk_charge_payment")
 * @ORM\Entity
 */
class ChargePayment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="charge_payment_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Charge", cascade={"persist", "remove"}, inversedBy="chargePayments")
     * @ORM\JoinColumn(name="fk_charge_id", referencedColumnName="charge_id", nullable=false, onDelete="CASCADE", unique=false)
     * @Assert\Type(type="\TSK\PaymentBundle\Entity\Charge")
     */
    protected $charge;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Payment", inversedBy="chargePayment", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_payment_id", referencedColumnName="payment_id", nullable=false, onDelete="CASCADE", unique=false)
     * @Assert\Type(type="\TSK\PaymentBundle\Entity\Payment")
     */
    protected $payment;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
     */
    private $amount;

    public function __construct()
    {
        // $this->chargePayments = new ArrayCollection();
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
     * @return ChargePayment
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
    public function setCharge($charge)
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
    public function setPayment($payment)
    {
        $this->payment = $payment;
        return $this;
    }
}
