<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use TSK\SchoolBundle\Entity\School;

/**
 * Payment
 *
 * @ORM\Table(name="tsk_payment")
 * @ORM\Entity(repositoryClass="TSK\PaymentBundle\Entity\PaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ExclusionPolicy("all")
 */
class Payment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="payment_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="legacy_payment_id", type="string")
     */
    private $legacyPaymentId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    private $school;


    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\PaymentType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_payment_type_id", referencedColumnName="payment_type_id", nullable=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\PaymentType")
     * @Expose
     */
    protected $paymentType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\PaymentMethod", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_payment_method_id", referencedColumnName="payment_method_id", nullable=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\PaymentMethod")
     */
    protected $paymentMethod;

    /**
     * @var float
     *
     * @ORM\Column(name="payment_amount", type="float")
     */
    private $paymentAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="ref_number", type="string", length=100, nullable=true)
     */
    private $refNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deferral_timestamp", type="datetime", nullable=true)
     */
    private $deferralTimestamp;

    /**
     * @var string
     *
     * @ORM\Column(name="authorization_num", type="string", length=100, nullable=true)
     */
    private $authorizationNum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdDate;

    /**
     * @var string
     *
     * @ORM\Column(name="created_user", type="string", length=100)
     * @Gedmo\Blameable(on="update")
     */
    private $createdUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_voided", type="boolean", nullable=true)
     */
    private $isVoided=false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_cash", type="boolean", nullable=true)
     */
    private $isCash=true;


    /**
     * @ORM\OneToMany(targetEntity="ChargePayment", mappedBy="payment", cascade={"all"})
     * @ORM\JoinTable(name="tsk_charge_payment",
     *      joinColumns={@ORM\JoinColumn(name="fk_payment_id", referencedColumnName="payment_id")}
     *  )
     * @Expose
     * */
    protected $chargePayments;

    public function __construct()
    {
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
     * Set paymentAmount
     *
     * @param float $paymentAmount
     * @return Payment
     */
    public function setPaymentAmount($paymentAmount)
    {
        $this->paymentAmount = $paymentAmount;
    
        return $this;
    }

    /**
     * Get paymentAmount
     *
     * @return float 
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Payment
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
     * Set refNumber
     *
     * @param string $refNumber
     * @return Payment
     */
    public function setRefNumber($refNumber)
    {
        $this->refNumber = $refNumber;
    
        return $this;
    }

    /**
     * Get refNumber
     *
     * @return string 
     */
    public function getRefNumber()
    {
        return $this->refNumber;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Payment
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
     * Set createdUser
     *
     * @param string $createdUser
     * @return Payment
     */
    public function setCreatedUser($createdUser)
    {
        $this->createdUser = $createdUser;
    
        return $this;
    }

    /**
     * Get createdUser
     *
     * @return string 
     */
    public function getCreatedUser()
    {
        return $this->createdUser;
    }

    /**
     * Set isVoided
     *
     * @param boolean $isVoided
     * @return Payment
     */
    public function setIsVoided($isVoided)
    {
        $this->isVoided = $isVoided;
    
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
    public function __toString()
    {
        return 'payment: ' . $this->getPaymentAmount();
    }
 
    /**
     * Get deferralTimestamp.
     *
     * @return deferralTimestamp.
     */
    public function getDeferralTimestamp()
    {
        return $this->deferralTimestamp;
    }
 
    /**
     * Set deferralTimestamp.
     *
     * @param deferralTimestamp the value to set.
     */
    public function setDeferralTimestamp($deferralTimestamp)
    {
        $this->deferralTimestamp = $deferralTimestamp;
        return $this;
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
     * Get authorizationNum.
     *
     * @return authorizationNum.
     */
    public function getAuthorizationNum()
    {
        return $this->authorizationNum;
    }
 
    /**
     * Set authorizationNum.
     *
     * @param authorizationNum the value to set.
     */
    public function setAuthorizationNum($authorizationNum)
    {
        $this->authorizationNum = $authorizationNum;
        return $this;
    }

    /**
     * Get legacyPaymentId.
     *
     * @return legacyPaymentId.
     */
    public function getLegacyChargeId()
    {
        return $this->legacyPaymentId;
    }
 
    /**
     * Set legacyPaymentId.
     *
     * @param legacyPaymentId the value to set.
     */
    public function setLegacyChargeId($legacyPaymentId)
    {
        $this->legacyPaymentId = $legacyPaymentId;
        return $this;
    }

    /**
     * prePersist 
     * 
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (!$this->getLegacyPaymentId()) {
            $t = microtime(true);
            $micro = sprintf("%06d",($t - floor($t)) * 1000000);
            $now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
            $this->setLegacyPaymentId($now->format('Ymdhis'));
        }
    }

    public function setLegacyPaymentId($legacyPaymentId)
    {
        $this->legacyPaymentId = $legacyPaymentId;
        return $this;
    }

    public function getLegacyPaymentId()
    {
        return $this->legacyPaymentId;
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
}
