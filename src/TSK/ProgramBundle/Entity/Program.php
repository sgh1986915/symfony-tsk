<?php
namespace TSK\ProgramBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\SchoolBundle\Entity\School;

/**
 * Program
 *
 * @ORM\Table(name="tsk_program", uniqueConstraints={@ORM\UniqueConstraint(name="program_legacy_id", columns={"legacy_program_id"})})
 * @ORM\Entity(repositoryClass="TSK\ProgramBundle\Entity\ProgramRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Program
{
    /**
     * @var integer
     *
     * @ORM\Column(name="program_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="legacy_program_id", type="string")
     */
    private $legacyProgramId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ProgramBundle\Entity\ProgramType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_program_type_id", referencedColumnName="program_type_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\ProgramBundle\Entity\ProgramType")
     */
    private $programType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ProgramBundle\Entity\MembershipType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_membership_type_id", referencedColumnName="membership_type_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\ProgramBundle\Entity\MembershipType")
     */
    private $membershipType;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\PaymentBundle\Entity\DiscountType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_discount_type_id", referencedColumnName="discount_id", nullable=false)
     * @Assert\Type(type="TSK\PaymentBundle\Entity\DiscountType")
     */
    protected $discountType;


    /**
     * @var string
     *
     * @ORM\Column(name="program_name", type="string", length=100)
     */
    private $programName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="legal_description", type="text", nullable=true)
     */
    private $legalDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="num_tokens", type="integer", nullable=true)
     */
    private $numTokens;

    /**
     * @var string
     *
     * @ORM\Column(name="duration_days", type="integer", nullable=true)
     */
    private $durationDays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiration_date", type="bigint", nullable=true)
     */
    private $expirationDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive=false;

    /**
     * @ORM\ManyToMany(targetEntity="\TSK\SchoolBundle\Entity\School")
     * @ORM\JoinTable(name="tsk_program_school",
     *      joinColumns={@ORM\JoinColumn(name="fk_program_id", referencedColumnName="program_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id")}
     *  )
     * */
    protected $schools;

    /**
     *
     * @ORM\OneToMany(targetEntity="ProgramPaymentPlan", mappedBy="program", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="tsk_program_payment_plan",
     *      joinColumns={@ORM\JoinColumn(name="fk_program_id", referencedColumnName="program_id")}
     * )
     */
    protected $paymentPlans;

    public function __construct()
    {
        $this->schools = new ArrayCollection();
        $this->paymentPlans = new ArrayCollection();
    }

    public function getPaymentPlans()
    {
        return $this->paymentPlans;
    }

    public function setPaymentPlans($paymentPlans)
    {
/*
        foreach ($this->paymentPlans as $paymentPlan) {
            if ($paymentPlans->contains($paymentPlan)) {
                $paymentPlans->removeElement($paymentPlan);
            } else {
                $this->removePaymentPlan($paymentPlan);
            }
        }
*/
        foreach ($paymentPlans as $idx => $pp) {
            $this->addPaymentPlan($pp);
        }
    }

    public function addPaymentPlan(ProgramPaymentPlan $paymentPlan)
    {
        $this->paymentPlans[] = $paymentPlan;
    }

    public function removePaymentPlan(ProgramPaymentPlan $paymentPlan)
    {
        return $this->paymentPlans->removeElement($paymentPlan);
    }

    public function getSchools()
    {
        return $this->schools;
    }

    public function setSchools($schools)
    {
        foreach ($schools as $idx => $school) {
            $this->addSchool($school);
        }
    }

    public function addSchool(School $school)
    {
        $this->schools[] = $school;
    }

    public function removeSchool(School $school)
    {
        return $this->schools->removeElement($school);
    }

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
     * Set program_type
     *
     * @param string $programType
     * @return Program
     */
    public function setProgramType($programType)
    {
        $this->programType = $programType;
    
        return $this;
    }

    /**
     * Get program_type
     *
     * @return string 
     */
    public function getProgramType()
    {
        return $this->programType;
    }

    /**
     * Set programName
     *
     * @param string $programName
     * @return Program
     */
    public function setProgramName($programName)
    {
        $this->programName = $programName;
    
        return $this;
    }

    /**
     * Get programName
     *
     * @return string 
     */
    public function getProgramName()
    {
        return $this->programName;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Program
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
     * Set expiration_date
     *
     * @param \DateTime $expirationDate
     * @return Program
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    
        return $this;
    }

    /**
     * Get expiration_date
     *
     * @return \DateTime 
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Program
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
     * Set isActive
     *
     * @param boolean $isActive
     * @return Program
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
        return (string) '(' . $this->getId() .') ' . $this->getProgramName();
    }

    public function getSelectLabel()
    {
        // How do we get program price?
        return $this->getProgramName() . ' ' . $this->getDiscountType()->getName();
        return sprintf("%s ($%5.2f)", $this->programName, 1000); 
    }
 
 /**
  * Get numTokens.
  *
  * @return numTokens.
  */
 public function getNumTokens()
 {
     return $this->numTokens;
 }
 
 /**
  * Set numTokens.
  *
  * @param numTokens the value to set.
  */
 public function setNumTokens($numTokens)
 {
     $this->numTokens = $numTokens;
     return $this;
 }

 /**
  * Get durationDays.
  *
  * @return durationDays.
  */
 public function getDurationDays()
 {
     return $this->durationDays;
 }
 
 /**
  * Set durationDays.
  *
  * @param durationDays the value to set.
  */
 public function setDurationDays($durationDays)
 {
     $this->durationDays = $durationDays;
     return $this;
 }
 
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
     * Get legalDescription.
     *
     * @return legalDescription.
     */
    public function getLegalDescription()
    {
        return $this->legalDescription;
    }
    
    /**
     * Set legalDescription.
     *
     * @param legalDescription the value to set.
     */
    public function setLegalDescription($legalDescription)
    {
        $this->legalDescription = $legalDescription;
        return $this;
    }

 
    /**
     * Get discountType.
     *
     * @return discountType.
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }
 
    /**
     * Set discountType.
     *
     * @param discountType the value to set.
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;
        return $this;
    }

 
    /**
     * Get legacyProgramId.
     *
     * @return legacyProgramId.
     */
    public function getLegacyProgramId()
    {
        return $this->legacyProgramId;
    }
    
    /**
     * Set legacyProgramId.
     *
     * @param legacyProgramId the value to set.
     */
    public function setLegacyProgramId($legacyProgramId)
    {
        $this->legacyProgramId = $legacyProgramId;
        return $this;
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
        if (!$this->getLegacyProgramId()) {
            $this->setLegacyProgramId($now->format('Ymdhis.u'));
        }
    }
}
