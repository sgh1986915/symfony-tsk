<?php
namespace TSK\PaymentBundle\Form\Model;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use TSK\contactBundle\Entity\Contact;
use TSK\PaymentBundle\Entity\PaymentMethod;
use TSK\PaymentBundle\Entity\PaymentType;

class ChargeCredit
{
    /**
     * contact 
     * 
     * @var mixed
     * @Assert\NotBlank()
     * @access private
     */
    private $contact;

    /**
     * paymentDate 
     * 
     * @var mixed
     * @access private
     */
    private $date;

    /**
     * paymentAmount 
     * @Assert\NotBlank()
     * 
     * @var mixed
     * @access private
     */
    private $paymentAmount;

    /**
     * paymentType
     * @Assert\NotBlank()
     * 
     * @var mixed
     * @access private
     */
    private $paymentType;

    private $memo;

    private $lineItems;

    public function __construct()
    {
        $this->lineItems = new ArrayCollection();
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
    public function setContact($contact)
    {
        $this->contact = $contact;
        return $this;
    }
 
    /**
     * Get paymentDate.
     *
     * @return paymentDate.
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }
 
    /**
     * Set paymentDate.
     *
     * @param paymentDate the value to set.
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }
 
    /**
     * Get paymentAmount.
     *
     * @return paymentAmount.
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }
 
    /**
     * Set paymentAmount.
     *
     * @param paymentAmount the value to set.
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;
        return $this;
    }
 
    /**
     * Get paymentMethod.
     *
     * @return paymentMethod.
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
 
    /**
     * Set paymentMethod.
     *
     * @param paymentMethod the value to set.
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }
 
    /**
     * Get refNumber.
     *
     * @return refNumber.
     */
    public function getRefNumber()
    {
        return $this->refNumber;
    }
 
    /**
     * Set refNumber.
     *
     * @param refNumber the value to set.
     */
    public function setRefNumber($refNumber)
    {
        $this->refNumber = $refNumber;
        return $this;
    }
 
    /**
     * Get memo.
     *
     * @return memo.
     */
    public function getMemo()
    {
        return $this->memo;
    }
 
    /**
     * Set memo.
     *
     * @param memo the value to set.
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
        return $this;
    }
    public function __toString()
    {
        return 'blah';
    }
 
    /**
     * Get creditCardNumber.
     *
     * @return creditCardNumber.
     */
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
    }
 
    /**
     * Set creditCardNumber.
     *
     * @param creditCardNumber the value to set.
     */
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = $creditCardNumber;
        return $this;
    }
 
    /**
     * Get creditCardExpirationDate.
     *
     * @return creditCardExpirationDate.
     */
    public function getCreditCardExpirationDate()
    {
        return $this->creditCardExpirationDate;
    }
 
    /**
     * Set creditCardExpirationDate.
     *
     * @param creditCardExpirationDate the value to set.
     */
    public function setCreditCardExpirationDate($creditCardExpirationDate)
    {
        $this->creditCardExpirationDate = $creditCardExpirationDate;
        return $this;
    }
 
    /**
     * Get cvvNumber.
     *
     * @return cvvNumber.
     */
    public function getCvvNumber()
    {
        return $this->cvvNumber;
    }
 
    /**
     * Set cvvNumber.
     *
     * @param cvvNumber the value to set.
     */
    public function setCvvNumber($cvvNumber)
    {
        $this->cvvNumber = $cvvNumber;
        return $this;
    }
 
 /**
  * Get charges.
  *
  * @return charges.
  */
 public function getCharges()
 {
     return $this->charges;
 }
 
 /**
  * Set charges.
  *
  * @param charges the value to set.
  */
 public function setCharges($charges)
 {
     $this->charges = $charges;
     return $this;
 }
 
 /**
  * Get paymentType.
  *
  * @return paymentType.
  */
 public function getPaymentType()
 {
     return $this->paymentType;
 }
 
 /**
  * Set paymentType.
  *
  * @param paymentType the value to set.
  */
 public function setPaymentType(PaymentType $paymentType)
 {
     $this->paymentType = $paymentType;
 }
}
?>

