<?php
namespace TSK\BilleeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\UserBundle\Entity\Contact;

/**
 * BilleePaymentMethod
 *
 * @ORM\Table(name="tsk_billee_payment_method")
 * @ORM\Entity
 */
class BilleePaymentMethod
{
    /**
     * @var integer
     *
     * @ORM\Column(name="billee_payment_method_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id", nullable=false, unique=false)
     */
    private $contact;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="fk_payment_method_id", referencedColumnName="payment_method_id", nullable=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\PaymentMethod")
     */
    private $paymentMethod;

    /**
     * @var string - for E4 TransArmor Token
     *
     * @ORM\Column(name="transarmor_token", type="string", length=255, nullable=true)
     */
    private $transArmorToken;

    /**
     * @var string
     *
     * @ORM\Column(name="cc_num", type="string", length=100, nullable=true)
     */
    private $cc_num;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cc_expiration_date", type="date", nullable=true)
     */
    private $cc_expiration_date;

    /**
     * @var string
     *
     * @ORM\Column(name="cvv_number", type="string", length=5, nullable=true)
     */
    private $cvv_number;

    /**
     * @var string
     *
     * @ORM\Column(name="routing_num", type="string", length=100, nullable=true)
     */
    private $routing_num;

    /**
     * @var string
     *
     * @ORM\Column(name="account_num", type="string", length=100, nullable=true)
     */
    private $account_num;


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
     * Set cc_num
     *
     * @param string $ccNum
     * @return BilleePaymentMethod
     */
    public function setCcNum($ccNum)
    {
        $this->cc_num = $ccNum;
    
        return $this;
    }

    /**
     * Get cc_num
     *
     * @return string 
     */
    public function getCcNum()
    {
        return $this->cc_num;
    }

    /**
     * Set cc_expiration_date
     *
     * @param \DateTime $ccExpirationDate
     * @return BilleePaymentMethod
     */
    public function setCcExpirationDate($ccExpirationDate)
    {
        $this->cc_expiration_date = $ccExpirationDate;
    
        return $this;
    }

    /**
     * Get cc_expiration_date
     *
     * @return \DateTime 
     */
    public function getCcExpirationDate()
    {
        return $this->cc_expiration_date;
    }

    /**
     * Set cvv_number
     *
     * @param string $cvvNumber
     * @return BilleePaymentMethod
     */
    public function setCvvNumber($cvvNumber)
    {
        $this->cvv_number = $cvvNumber;
    
        return $this;
    }

    /**
     * Get cvv_number
     *
     * @return string 
     */
    public function getCvvNumber()
    {
        return $this->cvv_number;
    }

    /**
     * Set routing_num
     *
     * @param string $routingNum
     * @return BilleePaymentMethod
     */
    public function setRoutingNum($routingNum)
    {
        $this->routing_num = $routingNum;
    
        return $this;
    }

    /**
     * Get routing_num
     *
     * @return string 
     */
    public function getRoutingNum()
    {
        return $this->routing_num;
    }

    /**
     * Set account_num
     *
     * @param string $accountNum
     * @return BilleePaymentMethod
     */
    public function setAccountNum($accountNum)
    {
        $this->account_num = $accountNum;
    
        return $this;
    }

    /**
     * Get account_num
     *
     * @return string 
     */
    public function getAccountNum()
    {
        return $this->account_num;
    }
 
 /**
  * Get paymentMethod.
  *
  * @return paymentMethod.
  */
 function getPaymentMethod()
 {
     return $this->paymentMethod;
 }
 
 /**
  * Set paymentMethod.
  *
  * @param paymentMethod the value to set.
  */
 function setPaymentMethod($paymentMethod)
 {
     $this->paymentMethod = $paymentMethod;
    return $this;
 }
 
 /**
  * Get contact.
  *
  * @return contact.
  */
 public function getContact()
 {
     return $this->contact;
 }
 
 /**
  * Set contact.
  *
  * @param contact the value to set.
  */
 public function setContact(Contact $contact)
 {
     $this->contact = $contact;
     return $this;
 }
 
    /**
     * Get token.
     *
     * @return token.
     */
    public function getTransArmorToken()
    {
        return $this->transArmorToken;
    }
 
    /**
     * Set token.
     *
     * @param token the value to set.
     */
    public function setTransArmorToken($transArmorToken)
    {
        $this->transArmorToken = $transArmorToken;
        return $this;
    }
}
