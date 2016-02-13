<?php

namespace TSK\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use TSK\ProgramBundle\Entity\Program;
use TSK\SchoolBundle\Entity\School;
use TSK\StudentBundle\Entity\Student;
use TSK\PaymentBundle\Entity\Charge;
use TSK\UserBundle\Entity\Organization;
use TSK\ContractBundle\Entity\ContractFreeze;
use TSK\ContractBundle\Entity\ContractDoc;

/**
 * Contract
 *
 * @ORM\Table(name="tsk_contract", uniqueConstraints={@ORM\UniqueConstraint(name="contract_legacy_id", columns={"legacy_contract_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class Contract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contract_id", type="integer")
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
     * @ORM\ManyToOne(targetEntity="TSK\ProgramBundle\Entity\Program")
     * @ORM\JoinColumn(name="fk_program_id", referencedColumnName="program_id", nullable=false)
     * @Assert\Type(type="TSK\ProgramBundle\Entity\Program")
     */
    private  $program;

    /**
     * @ORM\ManyToMany(targetEntity="\TSK\StudentBundle\Entity\Student", inversedBy="contracts")
     * @ORM\JoinTable(name="tsk_student_contract",
     *      joinColumns={@ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id")}
     *  )
     */
    protected $students;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School")
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    protected $school;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\PaymentBundle\Entity\Charge", inversedBy="contracts")
     * @ORM\JoinTable(name="tsk_contract_charge",
     *      joinColumns={@ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_charge_id", referencedColumnName="charge_id")}
     *      )
     */
    protected $charges;

    /**
     * @ORM\OneToMany(targetEntity="ContractFreeze", mappedBy="contract")
     **/
    protected $freezes;

    /**
     * @ORM\OneToMany(targetEntity="ContractDoc", mappedBy="contract", cascade={"all"})
     **/
    protected $docs;

    /**
     * @ORM\Column(name="credit", type="float", nullable=true)
     **/
    protected $credit;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="legacy_contract_id", type="string")
     */
    private $legacyContractId;

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
     * @Gedmo\Blameable(on="create")
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     */
    private $updatedBy;

    /**
     * @var datetime $updatedDate
     *
     * @Gedmo\Timestampable(on="create")
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(name="updated_date", type="datetime")
     */
    private $updatedDate;


    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->charges = new ArrayCollection();
        $this->freezes = new ArrayCollection();
        $this->docs = new ArrayCollection();
    }

    public function getStudents()
    {
        return $this->students;
    }

    public function setStudents($students)
    {
        foreach ($students as $idx => $student) {
            $this->addStudent($student);
        }
    }

    public function addStudent(Student $student)
    {
        $this->students[] = $student;
    }

    public function removeStudent(Student $student)
    {
        return $this->students->removeElement($student);
    }

    public function getFreezes()
    {
        return $this->freezes;
    }

    public function setFreezes($freezes)
    {
        foreach ($freezes as $idx => $freeze) {
            $this->addFreeze($freeze);
        }
    }

    public function addFreeze(ContractFreeze $freeze)
    {
        $this->freezes[] = $freeze;
    }

    public function removeFreeze(ContractFreeze $freeze)
    {
        return $this->freezes->removeElement($freeze);
    }

    public function getActiveFreeze()
    {
        $today = new \DateTime();
        foreach ($this->getFreezes() as $freeze) {
            if (($freeze->getStartDate() >= $today) && ($freeze->getEndDate() <= $today)) {
                return $freeze;
            }
        }
        return null;
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

    public function getProgram()
    {
        return $this->program;
    }

    public function setProgram(Program $program)
    {
        $this->program = $program;
        return $this;
    }

    public function getSchool()
    {
        return $this->school;
    }

    public function setSchool(School $school)
    {
        $this->school = $school;
        return $this;
    }

    public function getStudent()
    {
        return $this->student;
    }

    public function setStudent(Student $student)
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

    /**
     * Get deferralDurationMonths.
     *
     * @return deferralDurationMonths.
     */
    public function getDeferralDurationMonths()
    {
        return $this->deferralDurationMonths;
    }

    public function getRemainingDeferralPeriods()
    {
        $today = new \DateTime();
        $contractStartDate = clone $this->getContractStartDate();
        print "contract start date is " . $contractStartDate->format('Y-m-d') . "\n";
        $contractStartDate->modify('first day of next month');
        print "first day of following month is " . $contractStartDate->format('Y-m-d') . "\n";
        $contractStartDate->add(new \DateInterval('P' . $this->getDeferralDurationMonths() . 'M'));

        print " ... plus ".$this->getDeferralDurationMonths() ." months is " . $contractStartDate->format('Y-m-d') . "\n";

        $result = $contractStartDate->diff($today)->format('%m');
        return min($result, $this->getDeferralDurationMonths());
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
                $this->getProgram()->getProgramName()
            );
        } else {
            return (string) ' ';
        }
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
        if ($today > $contractExpireDate) {
            return 0;
        }
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

    public function getDaysConsumed()
    {
        $today = new \DateTime();
        $todayLessFreezeDays = $today->sub(new \DateInterval('P' . $this->getFreezeDays() . 'D'));
        $daysConsumed = $this->getContractStartDate()->diff($todayLessFreezeDays, true);
        return $daysConsumed->format('%a');
    }

    public function getDollarsConsumed()
    {
        return $this->getDayRate() * $this->getDaysConsumed();
        return round($this->getPercentageTimeConsumed() * $this->getPrincipal(), 2);
    }

    public function getCreditDue()
    {
        return $this->getAmountPaid() - $this->getDollarsConsumed();
    }

    public function getDollarsOwed()
    {
        return (1 - $this->getPercentageTimeConsumed()) * $this->getPrincipal();
    }
    
    public function getFreezeDays()
    {
        return 0;
    }

    public function getTotalDays()
    {
        return $this->getContractStartDate()->diff($this->getContractExpiry())->format('%R%a');
    }

    public function getDollarsRemaining()
    {
        $principal = $this->getPaymentTerms();
        $obj = json_decode($principal['paymentsData']);
        return ($this->getBalanceInDays() * $obj->principal) / $this->getTotalDays();
    }

    public function getPrincipal()
    {
        $paymentTerms = $this->getPaymentTerms();
        $obj = json_decode($paymentTerms['paymentsData']);
        return $obj->principal;
    }

    public function getDayRate()
    {
        return round($this->getPrincipal() / $this->getTotalDays(), 2);
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

    public function renderContractVersion()
    {
        $doc = new ContractDoc();
        $doc->setContract($this);
        $doc->setIsActive($this->getIsActive());
        $doc->setLegacyContractId($this->getLegacyContractId());
        $doc->setOrganization($this->getOrganization());
        $doc->setMembershipType($this->getProgram()->getMembershipType());

        $schoolCorp = $this->getSchool()->getContact()->getCorporations()->first();
        $doc->setSchoolLegalName($schoolCorp->getLegalName());
        $doc->setSchoolAbbrLegalName($schoolCorp->getAbbrLegalName());
        $doc->setSchoolDba($schoolCorp->getDba());
        $doc->setSchoolAddress1($this->getSchool()->getContact()->getAddress1());
        $doc->setSchoolAddress2($this->getSchool()->getContact()->getAddress2());
        $doc->setSchoolCity($this->getSchool()->getContact()->getCity());
        $doc->setSchoolState($this->getSchool()->getContact()->getState()->getId());
        $doc->setSchoolPostalCode($this->getSchool()->getContact()->getPostalCode());
        $doc->setSchoolPhone($this->getSchool()->getContact()->getPhone());
        $doc->setSchoolLateGraceDays($this->getSchool()->getLateGraceDays());
        $doc->setSchoolLatePaymentCharge($this->getSchool()->getLatePaymentCharge());
        foreach ($this->getStudents() as $student) {
            $doc->setStudents((string)$student);
        }
        $doc->setProgramName($this->getProgram()->getProgramName());
        $doc->setProgramDurationDays($this->getProgram()->getDurationDays());
        $doc->setProgramLegalDescription($this->getProgram()->getLegalDescription());
        $doc->setContractStartDate($this->getContractStartDate());
        $doc->setContractExpiry($this->getContractExpiry());
        $doc->setRolloverTokens($this->getRolloverTokens());
        $doc->setRolloverDays($this->getRolloverDays());
        $doc->setDeferralRate($this->getDeferralRate());
        $doc->setDeferralDurationMonths($this->getDeferralDurationMonths());
        $doc->setDeferralDistributionStrategy($this->getDeferralDistributionStrategy());
        // $doc->setContractNumPayments($this->getContractNumPayments());
        $doc->setContractNumTokens($this->getContractNumTokens());
        // $doc->setAmount($this->getAmount());
        // $doc->setDiscount($this->getDiscount());
        $doc->setPaymentTerms($this->getPaymentTerms());
        
        return $doc;
    }

    public function getDocs()
    {
        return $this->docs;
    }

    public function addDoc(ContractDoc $doc)
    {
        $this->docs[] = $doc;
    }

    public function setDocs($docs)
    {
        foreach ($docs as $idx => $doc) {
            $this->addDoc($doc);
        }
    }

    public function getLatestContractDoc()
    {
        $currentLatestDate = new \DateTime('2000-01-01');
        $latestDoc = null;
        foreach ($this->getDocs() as $doc) {
            if ($currentLatestDate < $doc->getCreatedDate()) {
                $currentLatestDate = $doc->getCreatedDate();    
                $latestDoc = $doc;
            }
        }
        return $latestDoc;
    }

    /**
     * prePersist 
     * 
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        if (!$this->getLegacyContractId()) {
            $this->setLegacyContractId($now->format('Ymdhis'));
        }
    }
 
    /**
     * Get credit.
     *
     * @return credit.
     */
    public function getCredit()
    {
        return $this->credit;
    }
 
    /**
     * Set credit.
     *
     * @param credit the value to set.
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
        return $this;
    }
}
