<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PaymentMethod
 *
 * @ORM\Table(name="tsk_ref_payment_method")
 * @ORM\Entity
 */
class PaymentMethod
{
    /**
     * @var integer
     *
     * @ORM\Column(name="payment_method_id", type="integer")
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
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\Account", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_account_id", referencedColumnName="account_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\Account")
     */
    protected $account;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=20)
     */
    private $paymentType;

    /**
     * @var string
     *
     * @ORM\Column(name="is_recurring", type="boolean", nullable=true)
     */
    private $isRecurring = false;

    /**
     * @var string
     *
     * @ORM\Column(name="is_receivable", type="boolean", nullable=true)
     */
    private $isReceivable = false;

    /**
     * @var string
     *
     * @ORM\Column(name="is_cash", type="boolean", nullable=true)
     */
    private $isCash = true;


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
     * @return PaymentMethods
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
     * Set payment_type
     *
     * @param string $paymentType
     * @return PaymentMethods
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;
    
        return $this;
    }

    /**
     * Get payment_type
     *
     * @return string 
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    public function getOrganization()
    {
        return $this->organization;
    }
    
    public function setOrganization(\TSK\UserBundle\Entity\Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }

    /**
     * Get isRecurring.
     *
     * @return isRecurring.
     */
    function getIsRecurring()
    {
        return $this->isRecurring;
    }
    
    /**
     * Set isRecurring.
     *
     * @param isRecurring the value to set.
     */
    function setIsRecurring($isRecurring)
    {
        $this->isRecurring = $isRecurring;
        return $this;
    }
 
    /**
     * Get isReceivable.
     *
     * @return isReceivable.
     */
    public function getIsReceivable()
    {
        return $this->isReceivable;
    }
 
    /**
     * Set isReceivable.
     *
     * @param isReceivable the value to set.
     */
    public function setIsReceivable($isReceivable)
    {
        $this->isReceivable = $isReceivable;
        return $this;
    }
 
    /**
     * Get isCash.
     *
     * @return isCash.
     */
    public function getIsCash()
    {
        return $this->isCash;
    }
 
    /**
     * Set isCash.
     *
     * @param isCash the value to set.
     */
    public function setIsCash($isCash)
    {
        $this->isCash = $isCash;
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
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }
}
