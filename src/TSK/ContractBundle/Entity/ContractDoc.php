<?php

namespace TSK\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
// use Doctrine\Common\Collections\ArrayCollection;
use TSK\ProgramBundle\Entity\Program;
use TSK\SchoolBundle\Entity\School;
use TSK\StudentBundle\Entity\Student;
use TSK\PaymentBundle\Entity\Charge;
use TSK\UserBundle\Entity\Organization;

/**
 * ContractDoc
 *
 * @ORM\Table(name="tsk_contract_doc")
 * @ORM\Entity
 */
class ContractDoc
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contract_doc_id", type="integer")
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
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", inversedBy="docs")
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    protected $contract;

    /**
     * @var integer
     *
     * @ORM\Column(name="legacy_contract_id", type="string", nullable=true)
     */
    private $legacyContractId;

    /**
     * @var string
     *
     * @ORM\Column(name="membership_type", type="string")
     */
    private  $membershipType;

    /**
     * @var string
     *
     * @ORM\Column(name="students", type="text")
     */
    protected $students;

    /**
     * @var string
     *
     * @ORM\Column(name="school_legal_name", type="string")
     */
    protected $schoolLegalName;

    /**
     * @var string
     *
     * @ORM\Column(name="school_abbr_legal_name", type="string", nullable=true)
     */
    protected $schoolAbbrLegalName;

    /**
     * @var string
     *
     * @ORM\Column(name="school_dba", type="string", nullable=true)
     */
    protected $schoolDBA;

    /**
     * @var string
     *
     * @ORM\Column(name="school_address1", type="string", nullable=true)
     */
    protected $schoolAddress1;

    /**
     * @var string
     *
     * @ORM\Column(name="school_address2", type="string", nullable=true)
     */
    protected $schoolAddress2;

    /**
     * @var string
     *
     * @ORM\Column(name="school_city", type="string", nullable=true)
     */
    protected $schoolCity;

    /**
     * @var string
     *
     * @ORM\Column(name="school_state", type="string", nullable=true)
     */
    protected $schoolState;

    /**
     * @var string
     *
     * @ORM\Column(name="school_postal_code", type="string", nullable=true)
     */
    protected $schoolPostalCode;

    /**
     * @var string
     *
     * @ORM\Column(name="school_phone", type="string", nullable=true)
     */
    protected $schoolPhone;

    /**
     * @var string
     *
     * @ORM\Column(name="school_late_grace_days", type="string", nullable=true)
     */
    protected $schoolLateGraceDays;

    /**
     * @var string
     *
     * @ORM\Column(name="school_late_payment_charge", type="string", nullable=true)
     */
    protected $schoolLatePaymentCharge;


    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive=false;


    /**
     * @var integer
     *
     * @ORM\Column(name="program_name", type="string")
     */
    private $programName;

    /**
     * @var integer
     *
     * @ORM\Column(name="program_duration_days", type="integer", nullable=false)
     */
    private $programDurationDays;

    /**
     * @var integer
     *
     * @ORM\Column(name="program_legal_description", type="text")
     */
    private $programLegalDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_start_date", type="date", nullable=false)
     */
    private $contractStartDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_expire_date", type="date", nullable=true)
     */
    private $contractExpiry;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_num_tokens", type="integer", nullable=true)
     */
    private $contractNumTokens;

    /**
     * @var integer
     *
     * @ORM\Column(name="rollover_tokens", type="integer", nullable=true)
     */
    private $rolloverTokens;

    /**
     * @var integer
     *
     * @ORM\Column(name="rollover_days", type="integer", nullable=true)
     */
    private $rolloverDays;

    /**
     * @var integer
     *
     * @ORM\Column(name="deferral_rate", type="decimal", precision=3, scale=2, nullable=true)
     */
    private $deferralRate;

    /**
     * @var integer
     *
     * @ORM\Column(name="deferral_duration_months", type="smallint", nullable=true)
     */
    private $deferralDurationMonths;

    /**
     * @var string
     *
     * @ORM\Column(name="deferral_distribution_strategy", type="string", nullable=true)
     */
    private $deferralDistributionStrategy;

    /**
     * @var integer
     *
     * @ORM\Column(name="contract_num_payments", type="smallint", nullable=true)
     */
    private $contractNumPayments;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", nullable=true)
     */
    private $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", nullable=true)
     */
    private $discount;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_terms", type="array", nullable=false)
     */
    private $payment_terms;

    /**
     * @var string $createdBy
     *
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="created_by", type="string", nullable=true)
     */
    private $createdBy;

    /**
     * @var datetime $createdDate
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="created_date", type="datetime")
     */
    private $createdDate;

    /**
     * @var string $updatedBy
     *
     * @Gedmo\Blameable(on="update")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @var datetime $updatedDate
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updatedDate;


    public function __construct()
    {
        // $this->students = new ArrayCollection();
        // $this->charges = new ArrayCollection();
    }

    public function getStudents()
    {
        return $this->students;
    }

    public function setStudents($students)
    {
        $this->students = $students;
        return $this;
    }

    public function addStudent(Student $student)
    {
        $this->students[] = $student;
    }

    public function removeStudent(Student $student)
    {
        return $this->students->removeElement($student);
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
     * Set legacy_id
     *
     * @param integer $legacyId
     * @return Contract
     */
    public function setLegacyContractId($legacyContractId)
    {
        $this->legacyContractId = $legacyContractId;
    
        return $this;
    }

    /**
     * Get legacy_id
     *
     * @return integer 
     */
    public function getLegacyContractId()
    {
        return $this->legacyContractId;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Contract
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
     * Set payment_terms
     *
     * @param string $paymentTerms
     * @return Contract
     */
    public function setPaymentTerms($paymentTerms)
    {
        $this->payment_terms = $paymentTerms;
    
        return $this;
    }

    /**
     * Get payment_terms
     *
     * @return string 
     */
    public function getPaymentTerms()
    {
        return $this->payment_terms;
    }

    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function setStudent($student)
    {
        $this->student = $student;
        return $this;
    }
 
 
 
    /**
     * Get contractExpiry.
     *
     * @return contractExpiry.
     */
    public function getContractExpiry()
    {
        return $this->contractExpiry;
    }
 
    /**
     * Set contractExpiry.
     *
     * @param contractExpiry the value to set.
     */
    public function setContractExpiry($contractExpiry)
    {
        $this->contractExpiry = $contractExpiry;
        return $this;
    }
 
    /**
     * Get contractNumTokens.
     *
     * @return contractNumTokens.
     */
    public function getContractNumTokens()
    {
        return $this->contractNumTokens;
    }
 
    /**
     * Set contractNumTokens.
     *
     * @param contractNumTokens the value to set.
     */
    public function setContractNumTokens($contractNumTokens)
    {
        $this->contractNumTokens = $contractNumTokens;
        return $this;
    }
 
    /**
     * Get programName.
     *
     * @return programName.
     */
    public function getProgramName()
    {
        return $this->programName;
    }
 
    /**
     * Set programName.
     *
     * @param programName the value to set.
     */
    public function setProgramName($programName)
    {
        $this->programName = $programName;
        return $this;
    }

/*
    public function getCharges()
    {
        return $this->charges;
    }

    public function setCharges($charges)
    {
        foreach ($charges as $idx => $charge) {
            $this->addCharge($charge);
        }
    }

    public function addCharge(Charge $charge)
    {
        $this->charges[] = $charge;
    }

    public function removeCharge(Charge $charge)
    {
        return $this->charges->removeElement($charge);
    }

    public function getChargesAsArray()
    {
        $result = array();
        foreach ($this->getCharges() as $chg) {
            $result[] = $chg->getAmount();
        }
        return $result;
    }
*/
 
    /**
     * Get contractNumPayments.
     *
     * @return contractNumPayments.
     */
    public function getContractNumPayments()
    {
        return $this->contractNumPayments;
    }
 
    /**
     * Set contractNumPayments.
     *
     * @param contractNumPayments the value to set.
     */
    public function setContractNumPayments($contractNumPayments)
    {
        $this->contractNumPayments = $contractNumPayments;
        return $this;
    }
 
    /**
     * Get deferralDurationMonths.
     *
     * @return deferralDurationMonths.
     */
    public function getDeferralDurationMonths()
    {
        return $this->deferralDurationMonths;
    }
 
    /**
     * Set deferralDurationMonths.
     *
     * @param deferralDurationMonths the value to set.
     */
    public function setDeferralDurationMonths($deferralDurationMonths)
    {
        $this->deferralDurationMonths = $deferralDurationMonths;
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
 }

/*
    public function __toString()
    {
        foreach ($this->getStudents() as $s) {
            $fn = $s->getContact()->getFirstName();
            $ln = $s->getContact()->getLastName();
            break;
        }
        if ($this->getSchool()) {
            return sprintf("%s %s: %s %s: %s", 
                $this->getSchool()->getContact()->getFirstName(),
                $this->getSchool()->getContact()->getLastName(),
                $fn,
                $ln,
                $this->getProgramName()
            );
        } else {
            return (string) ' ';
        }
    }
*/
  
     /**
      * Get membershipType.
      *
      * @return membershipType.
      */
     public function getMembershipType()
     {
         return $this->membershipType;
     }
  
     /**
      * Set membershipType.
      *
      * @param membershipType the value to set.
      */
     public function setMembershipType($membershipType)
     {
         $this->membershipType = $membershipType;
         return $this;
     }
 
    /**
     * Get programLegalDescription.
     *
     * @return programLegalDescription.
     */
    public function getProgramLegalDescription()
    {
        return $this->programLegalDescription;
    }
    
    /**
     * Set programLegalDescription.
     *
     * @param programLegalDescription the value to set.
     */
    public function setProgramLegalDescription($programLegalDescription)
    {
        $this->programLegalDescription = $programLegalDescription;
    }
 
    /**
     * Get rolloverTokens.
     *
     * @return rolloverTokens.
     */
    public function getRolloverTokens()
    {
        return $this->rolloverTokens;
    }
 
    /**
     * Set rolloverTokens.
     *
     * @param rolloverTokens the value to set.
     */
    public function setRolloverTokens($rolloverTokens)
    {
        $this->rolloverTokens = $rolloverTokens;
        return $this;
    }
 
    /**
     * Get isActive.
     *
     * @return isActive.
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
 
    /**
     * Set isActive.
     *
     * @param isActive the value to set.
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
 
    /**
     * Get discountAmount.
     *
     * @return discount.
     */
    public function getDiscount()
    {
        return $this->discount;
    }
 
    /**
     * Set discount.
     *
     * @param discount the value to set.
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }
 
    /**
     * Get rolloverDays.
     *
     * @return rolloverDays.
     */
    public function getRolloverDays()
    {
        return $this->rolloverDays;
    }
 
    /**
     * Set rolloverDays.
     *
     * @param rolloverDays the value to set.
     */
    public function setRolloverDays($rolloverDays)
    {
        $this->rolloverDays = $rolloverDays;
        return $this;
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function getOrganization()
    {
        return $this->organization;
    }


    public function getBalanceInDays()
    {
        $today = new \DateTime();
        $contractExpireDate = $this->getContractExpiry();
        $contractExpireDate->add(new \DateInterval('P' . $this->getFreezeDays() . 'D'));
        $timeBalance = $today->diff($contractExpireDate);
        return $timeBalance->format('%R%a');
    }

    public function getBalanceInDollars()
    {
        $amountDue = 0;
        foreach ($this->getCharges() as $charge) {
            $amountDue += $charge->getOpenAmount();
        }
        return $amountDue;
    }

    public function getAmountPaid()
    {
        $amountPaid = 0;
        foreach ($this->getCharges() as $charge) {
            $amountPaid += $charge->getPaidAmount();
        }
        return $amountPaid;
    }

    public function getTrueBalanceInDollars()
    {
        return $this->getAmountPaid() - $this->getDollarsConsumed();
    }

    public function getPercentageTimeConsumed()
    {
        $today = new \DateTime();
        $totalDays = $this->getContractStartDate()->diff($this->getContractExpiry())->format('%R%a');
        $foo = $this->getContractStartDate()->diff($today);

        $balance = $foo->format('%R%a');
        $daysConsumed = (int)$balance + $this->getFreezeDays();
        return round($daysConsumed / $totalDays, 4);
    }

    public function getDollarsConsumed()
    {
        return round($this->getPercentageTimeConsumed() * $this->getAmount(), 2);
    }

    public function getDollarsOwed()
    {
        return (1 - $this->getPercentageTimeConsumed()) * $this->getAmount();
    }
    
    public function getFreezeDays()
    {
        return 0;
    }

    public function getTotalDays()
    {
        return $this->getContractStartDate()->diff($this->getContractExpiry())->format('%R%a');
    }
 
    /**
     * Get programDurationDays.
     *
     * @return programDurationDays.
     */
    public function getProgramDurationDays()
    {
        return $this->programDurationDays;
    }
 
    /**
     * Set programDurationDays.
     *
     * @param programDurationDays the value to set.
     */
    public function setProgramDurationDays($programDurationDays)
    {
        $this->programDurationDays = $programDurationDays;
        return $this;
    }
 
    /**
     * Get contractStartDate.
     *
     * @return contractStartDate.
     */
    public function getContractStartDate()
    {
        return $this->contractStartDate;
    }
 
    /**
     * Set contractStartDate.
     *
     * @param contractStartDate the value to set.
     */
    public function setContractStartDate($contractStartDate)
    {
        $this->contractStartDate = $contractStartDate;
        return $this;
    }

    public function getContract()
    {
        return $this->contract;
    }

    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function __toString()
    {
        return (string) $this->getProgramName();
    }
 
    /**
     * Get schoolLegalName.
     *
     * @return schoolLegalName.
     */
    public function getSchoolLegalName()
    {
        return $this->schoolLegalName;
    }
 
    /**
     * Set schoolLegalName.
     *
     * @param schoolLegalName the value to set.
     */
    public function setSchoolLegalName($schoolLegalName)
    {
        $this->schoolLegalName = $schoolLegalName;
        return $this;
    }
 
    /**
     * Get schoolAbbrLegalName.
     *
     * @return schoolAbbrLegalName.
     */
    public function getSchoolAbbrLegalName()
    {
        return $this->schoolAbbrLegalName;
    }
 
    /**
     * Set schoolAbbrLegalName.
     *
     * @param schoolAbbrLegalName the value to set.
     */
    public function setSchoolAbbrLegalName($schoolAbbrLegalName)
    {
        $this->schoolAbbrLegalName = $schoolAbbrLegalName;
        return $this;
    }
 
    /**
     * Get schoolDBA.
     *
     * @return schoolDBA.
     */
    public function getSchoolDBA()
    {
        return $this->schoolDBA;
    }
 
    /**
     * Set schoolDBA.
     *
     * @param schoolDBA the value to set.
     */
    public function setSchoolDBA($schoolDBA)
    {
        $this->schoolDBA = $schoolDBA;
        return $this;
    }
 
    /**
     * Get schoolAddress1.
     *
     * @return schoolAddress1.
     */
    public function getSchoolAddress1()
    {
        return $this->schoolAddress1;
    }
 
    /**
     * Set schoolAddress1.
     *
     * @param schoolAddress1 the value to set.
     */
    public function setSchoolAddress1($schoolAddress1)
    {
        $this->schoolAddress1 = $schoolAddress1;
        return $this;
    }
 
    /**
     * Get schoolAddress2.
     *
     * @return schoolAddress2.
     */
    public function getSchoolAddress2()
    {
        return $this->schoolAddress2;
    }
 
    /**
     * Set schoolAddress2.
     *
     * @param schoolAddress2 the value to set.
     */
    public function setSchoolAddress2($schoolAddress2)
    {
        $this->schoolAddress2 = $schoolAddress2;
        return $this;
    }
 
    /**
     * Get schoolCity.
     *
     * @return schoolCity.
     */
    public function getSchoolCity()
    {
        return $this->schoolCity;
    }
 
    /**
     * Set schoolCity.
     *
     * @param schoolCity the value to set.
     */
    public function setSchoolCity($schoolCity)
    {
        $this->schoolCity = $schoolCity;
        return $this;
    }
 
    /**
     * Get schoolState.
     *
     * @return schoolState.
     */
    public function getSchoolState()
    {
        return $this->schoolState;
    }
 
    /**
     * Set schoolState.
     *
     * @param schoolState the value to set.
     */
    public function setSchoolState($schoolState)
    {
        $this->schoolState = $schoolState;
        return $this;
    }
 
    /**
     * Get schoolPostalCode.
     *
     * @return schoolPostalCode.
     */
    public function getSchoolPostalCode()
    {
        return $this->schoolPostalCode;
    }
 
    /**
     * Set schoolPostalCode.
     *
     * @param schoolPostalCode the value to set.
     */
    public function setSchoolPostalCode($schoolPostalCode)
    {
        $this->schoolPostalCode = $schoolPostalCode;
        return $this;
    }
 
 /**
  * Get schoolLateGraceDays.
  *
  * @return schoolLateGraceDays.
  */
 function getSchoolLateGraceDays()
 {
     return $this->schoolLateGraceDays;
 }
 
 /**
  * Set schoolLateGraceDays.
  *
  * @param schoolLateGraceDays the value to set.
  */
 function setSchoolLateGraceDays($schoolLateGraceDays)
 {
     $this->schoolLateGraceDays = $schoolLateGraceDays;
 }
 
 /**
  * Get schoolLatePaymentCharge.
  *
  * @return schoolLatePaymentCharge.
  */
 function getSchoolLatePaymentCharge()
 {
     return $this->schoolLatePaymentCharge;
 }
 
 /**
  * Set schoolLatePaymentCharge.
  *
  * @param schoolLatePaymentCharge the value to set.
  */
 function setSchoolLatePaymentCharge($schoolLatePaymentCharge)
 {
     $this->schoolLatePaymentCharge = $schoolLatePaymentCharge;
 }
 
 /**
  * Get schoolPhone.
  *
  * @return schoolPhone.
  */
 function getSchoolPhone()
 {
     return $this->schoolPhone;
 }
 
 /**
  * Set schoolPhone.
  *
  * @param schoolPhone the value to set.
  */
 function setSchoolPhone($schoolPhone)
 {
     $this->schoolPhone = $schoolPhone;
 }
}
