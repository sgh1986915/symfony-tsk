<?php

namespace TSK\ProgramBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\PaymentBundle\Entity\PaymentTerm;
use Doctrine\ORM\Mapping as ORM;
use TSK\ProgramBundle\Model\ProgramPaymentPlan as BaseProgramPaymentPlan;

/**
 * ProgramPaymentPlan
 *
 * @ORM\Table(name="tsk_program_payment_plan")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class ProgramPaymentPlan extends BaseProgramPaymentPlan
{
    /**
     * @var integer
     *
     * @ORM\Column(name="program_payment_plan_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     *
     * @ORM\ManyToOne(targetEntity="Program", inversedBy="paymentPlans", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_program_id", referencedColumnName="program_id", nullable=false)
     * @Assert\Type(type="TSK\ProgramBundle\Entity\Program")
     */
    protected $program;

    /*
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\PaymentTerm")
     * @ORM\JoinColumn(name="fk_payment_term_id", referencedColumnName="payment_term_id", nullable=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\PaymentTerm")
    protected $payment_term;
     */

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ProgramBundle\Entity\PaymentPlanType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_payment_plan_type_id", referencedColumnName="payment_plan_type_id", nullable=false)
     * @Assert\Type(type="TSK\ProgramBundle\Entity\PaymentPlanType")
    protected $paymentPlanType;
     */


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    protected $price;

    /**
     * minPayments 
     * 
     * @ORM\Column(name="min_payments", type="smallint", nullable=true)
     */
    protected $minPayments;

    /**
     * deferralDurationMonths 
     * 
     * @ORM\Column(name="deferral_duration_months", type="smallint", nullable=true)
     */
    protected $deferralDurationMonths;

    /**
     * deferralRate
     * @Assert\Range(     
     *      min = 0.01,
     *      max = 1,
     *      minMessage = "Deferral rate must be at least 0.01",
     *      maxMessage = "Deferral rate must be <= 1"
     * )
     * @ORM\Column(name="deferral_rate", type="float", nullable=true)
     */
    protected $deferralRate;

    /**
     * recognitionRate
     * 
     * @ORM\Column(name="recognition_rate", type="float", nullable=true)
     */
    protected $recognitionRate;

    /**
     * recognitionCap
     * 
     * @ORM\Column(name="recognition_cap", type="float", nullable=true)
     */
    protected $recognitionCap;

    /**
     * deferralDistributionStrategy
     * 
     * @ORM\Column(name="deferral_distribution_strategy", type="string", nullable=true)
     */
    protected $deferralDistributionStrategy;

    /**
     * @var string
     *
     * @ORM\Column(name="paymentsData", type="array")
     */
    protected $paymentsData;

    /**
     * @var string
     *
     * @ORM\Column(name="max_discount_percentage", type="string", length=10, nullable=true)
    protected $discount_percentage;
     */

    /**
     * @var float
     *
     * @ORM\Column(name="max_discount_dollar_amount", type="float", nullable=true)
    protected $discount_dollar_amount;
     */

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive=false;

    protected $selectLabel;


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
     * @return ProgramPaymentPlan
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
     * Set price
     *
     * @param float $price
     * @return ProgramPaymentPlan
     */
    public function setPrice($price)
    {
        $this->price = $price;
    
        return $this;
    }

    /**
     * Get price
     *
     * @return float 
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set paymentsData
     *
     * @param string $paymentsData
     * @return ProgramPaymentPlan
     */
    public function setPaymentsData($paymentsData)
    {
        $this->paymentsData = $paymentsData;
    
        return $this;
    }

    public function getPaymentsDataValue($key)
    {
        $data = $this->getPaymentsData(); 
        $json = (!empty($data['paymentsData'])) ? $data['paymentsData'] : '';
        $obj = json_decode($json);
        if (!empty($obj->$key)) {
            return $obj->$key;
        }
    }
    
    public function getSelectLabel()
    {
        return sprintf("%s [%s]", $this->getName(), $this->getPaymentsDataValue('summary'));
    }

    /**
     * Get paymentsData
     *
     * @return string 
     */
    public function getPaymentsData()
    {
        return $this->paymentsData;
    }

    /**
     * Set discount_percentage
     *
     * @param string $discountPercentage
     * @return ProgramPaymentPlan
     */
    public function setDiscountPercentage($discountPercentage)
    {
        $this->discount_percentage = $discountPercentage;
    
        return $this;
    }

    /**
     * Get discount_percentage
     *
     * @return string 
     */
    public function getDiscountPercentage()
    {
        return $this->discount_percentage;
    }

    /**
     * Set discount_dollar_amount
     *
     * @param float $discountDollarAmount
     * @return ProgramPaymentPlan
     */
    public function setDiscountDollarAmount($discountDollarAmount)
    {
        $this->discount_dollar_amount = $discountDollarAmount;
    
        return $this;
    }

    /**
     * Get discount_dollar_amount
     *
     * @return float 
     */
    public function getDiscountDollarAmount()
    {
        return $this->discount_dollar_amount;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return ProgramPaymentPlan
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setProgram(Program $program)
    {
        $this->program = $program;
        return $this;
    }

    public function getProgram()
    {
        return $this->program;
    }


    public function setDeferralDurationMonths($deferralDurationMonths)
    {
        $this->deferralDurationMonths = $deferralDurationMonths;
        return $this;
    }

    public function getDeferralDurationMonths()
    {
        return $this->deferralDurationMonths;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
 
 /**
  * Get paymentPlanType.
  *
  * @return paymentPlanType.
  */
 public function getPaymentPlanType()
 {
     return $this->paymentPlanType;
 }
 
 /**
  * Set paymentPlanType.
  *
  * @param paymentPlanType the value to set.
  */
 public function setPaymentPlanType($paymentPlanType)
 {
     $this->paymentPlanType = $paymentPlanType;
     return $this;
 }
 
    /**
     * Get maxPayments.
     *
     * @return maxPayments.
     */
    public function getMaxPayments()
    {
        return $this->maxPayments;
    }
 
    /**
     * Set maxPayments.
     *
     * @param maxPayments the value to set.
     */
    public function setMaxPayments($maxPayments)
    {
        $this->maxPayments = $maxPayments;
        return $this;
    }
 
    /**
     * Get minPayments.
     *
     * @return minPayments.
     */
    public function getMinPayments()
    {
        return $this->minPayments;
    }
 
    /**
     * Set minPayments.
     *
     * @param minPayments the value to set.
     */
    public function setMinPayments($minPayments)
    {
        $this->minPayments = $minPayments;
        return $this;
    }
 
    /**
     * Get deferralRate.
     *
     * @return deferralRate.
     */
    public function getDeferralRate()
    {
        return $this->deferralRate;
    }
 
    /**
     * Set deferralRate.
     *
     * @param deferralRate the value to set.
     */
    public function setDeferralRate($deferralRate)
    {
        $this->deferralRate = $deferralRate;
        return $this;
    }
 
    /**
     * Get deferralDistributionStrategy.
     *
     * @return deferralDistributionStrategy.
     */
    public function getDeferralDistributionStrategy()
    {
        return $this->deferralDistributionStrategy;
    }
 
    /**
     * Set deferralDistributionStrategy.
     *
     * @param deferralDistributionStrategy the value to set.
     */
    public function setDeferralDistributionStrategy($deferralDistributionStrategy)
    {
        $this->deferralDistributionStrategy = $deferralDistributionStrategy;
        return $this;
    }
 
    /**
     * Get recognitionRate.
     *
     * @return recognitionRate.
     */
    public function getRecognitionRate()
    {
        return $this->recognitionRate;
    }
 
    /**
     * Set recognitionRate.
     *
     * @param recognitionRate the value to set.
     */
    public function setRecognitionRate($recognitionRate)
    {
        $this->recognitionRate = $recognitionRate;
        return $this;
    }
 
    /**
     * Get recognitionCap.
     *
     * @return recognitionCap.
     */
    public function getRecognitionCap()
    {
        return $this->recognitionCap;
    }
 
    /**
     * Set recognitionCap.
     *
     * @param recognitionCap the value to set.
     */
    public function setRecognitionCap($recognitionCap)
    {
        $this->recognitionCap = $recognitionCap;
        return $this;
    }

    /**
     * prePersist 
     * 
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        if (!$this->getRecognitionCap()) {
            // May need json error detection here ...
            $obj = $this->getPaymentsData();
            $pd = $obj['paymentsData'];
            $obj = json_decode($pd);
            $this->setRecognitionCap($obj->principal);
        }
        if (!$this->getRecognitionRate()) {
            $this->setRecognitionRate(1 - $this->getDeferralRate());
        }
    }

}
