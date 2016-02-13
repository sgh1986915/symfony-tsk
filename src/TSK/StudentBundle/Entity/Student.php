<?php

namespace TSK\StudentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TSK\ContractBundle\Entity\Contract;
use TSK\RankBundle\Entity\Rank;
use Doctrine\Common\Collections\ArrayCollection;
use TSK\UserBundle\Entity\Contact;

use APY\DataGridBundle\Grid\Mapping as GRID;
use TSK\StudentBundle\Service\StudentService;


/**
 * Student
 *
 * @ORM\Table(name="tsk_student", uniqueConstraints={@ORM\UniqueConstraint(name="student_legacy_id", columns={"legacy_student_id"})})
 * @ORM\Entity(repositoryClass="TSK\StudentBundle\Entity\StudentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Student
{
    /**
     * @var integer
     *
     * @ORM\Column(name="student_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="legacy_student_id", type="string")
     */
    private $legacyStudentId;

    /**
     *
     * @ORM\OneToOne(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id", nullable=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Contact")
     *
     * @GRID\Column(field="contact.firstName", title="First Name")
     * @GRID\Column(field="contact.lastName", title="Last Name", filterable=false)
     */
    protected $contact;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\ContractBundle\Entity\Contract", mappedBy="students")
     */
    private $contracts;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\StudentStatus", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_student_status_id", referencedColumnName="student_status_id", nullable=false)
     * @Assert\Type(type="TSK\StudentBundle\Entity\StudentStatus")
     */
    protected $studentStatus;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\RankBundle\Entity\Rank", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rank_id", referencedColumnName="rank_id", nullable=true)
     * @Assert\Type(type="TSK\RankBundle\Entity\Rank")
     */
    protected $rank;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\RankBundle\Entity\Rank", cascade={"persist"})
     * @ORM\JoinTable(name="tsk_student_eligible_ranks",
     *      joinColumns={@ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_rank_id", referencedColumnName="rank_id")}
     *  )
     * */
    protected $eligibleRanks;

    protected $studentService;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinTable(name="tsk_student_emergency_contacts",
     *      joinColumns={@ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id")}
     *  )
     */
    protected $emergencyContacts;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist"})
     * @ORM\JoinTable(name="tsk_student_billees",
     *      joinColumns={@ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id")}
     *  )
     */
    protected $billees;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_prospective", type="boolean", nullable=true)
     */
    protected $isProspective=false;

    public function __construct()
    {
        $this->eligibleRanks = new ArrayCollection();
        $this->emergencyContacts = new ArrayCollection();
        $this->contracts = new ArrayCollection();
        $this->billees = new ArrayCollection();
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
     * setId 
     * 
     * @param mixed $id 
     * @access public
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }


    public function setStudentService(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function getStudentService()
    {
        return $this->studentService;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact(\TSK\UserBundle\Entity\Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function __toString()
    {
        if ($this->getContact()) {
            return (string) $this->getContact();
        } else {
            return '<new student>';
        }
    }
 
 /**
  * Get studentStatus.
  *
  * @return studentStatus.
  */
 public function getStudentStatus()
 {
     return $this->studentStatus;
 }
 
 /**
  * Set studentStatus.
  *
  * @param studentStatus the value to set.
  */
 public function setStudentStatus($studentStatus)
 {
     $this->studentStatus = $studentStatus;
     return $this;
 }

    public function getActiveContract()
    {
        foreach ($this->getContracts() as $contract) {
            if ($contract->getIsActive()) {
                return $contract;
            }
        }
        return null;
    }

    public function getContracts()
    {
        return $this->contracts;
    }

    public function setContracts($contracts)
    {
        foreach ($contracts as $idx => $contract) {
            $this->addContract($contract);
        }
    }

    public function addContract(Contract $contract)
    {
        if (!$this->getContracts()->contains($contract)) {
            $this->contracts[] = $contract;
        }
    }

    public function removeContract(Contract $contract)
    {
        return $this->contracts->removeElement($contract);
    }

 
    /**
     * Get rank.
     *
     * @return rank.
     */
    public function getRank()
    {
        return $this->rank;
    }
 
    /**
     * Set rank.
     *
     * @param rank the value to set.
     */
    public function setRank(Rank $rank)
    {
        $this->rank = $rank;
        return $this;
    }
 
    /**
     * Get eligibleRank.
     *
     * @return eligibleRank.
     */
    public function getEligibleRanks()
    {
        return $this->eligibleRanks;
    }
    
    /**
     * Set eligibleRank.
     *
     * @param eligibleRank the value to set.
     */
    public function setEligibleRanks($eligibleRanks)
    {
        foreach ($eligibleRanks as $er) {
            $this->addEligibleRank($er);
        }
    }

    public function addEligibleRank(Rank $rank)
    {
        if (!$this->getEligibleRanks()->contains($rank)) {
            $this->eligibleRanks[] = $rank;
        }
    }

    public function removeEligibleRank(Rank $rank)
    {
        return $this->eligibleRanks->removeElement($rank);
    }

    public function clearEligibleRanks()
    {
        foreach ($this->getEligibleRanks() as $er) {
            $this->removeEligibleRank($er);
        }
    }



    /**
     * Get emergencyContact.
     *
     * @return emergencyContact.
     */
    public function getEmergencyContacts()
    {
        return $this->emergencyContacts;
    }
    
    /**
     * Set emergencyContact.
     *
     * @param emergencyContact the value to set.
     */
    public function setEmergencyContacts($emergencyContacts)
    {
        foreach ($emergencyContacts as $ec) {
            $this->addEmergencyContact($ec);
        }
    }

    /**
     * addEmergencyContact 
     * Note how we only add an ec if we don't already have it
     * 
     * @param Contact $contact 
     * @access public
     * @return void
     */
    public function addEmergencyContact(Contact $contact)
    {
        if (!$this->getEmergencyContacts()->contains($contact)) {
            $this->emergencyContacts[] = $contact;
        }
    }

    public function removeEmergencyContact(Contact $contact)
    {
        return $this->emergencyContacts->removeElement($contact);
    }

    public function clearEmergencyContacts()
    {
        foreach ($this->getEmergencyContacts() as $ec) {
            $this->removeEmergencyContact($ec);
        }
    }

    /**
     * Get billee.
     *
     * @return billee.
     */
    public function getBillees()
    {
        return $this->billees;
    }
    
    /**
     * Set billee.
     *
     * @param billee the value to set.
     */
    public function setBillees($billees)
    {
        foreach ($billees as $ec) {
            $this->addBillee($ec);
        }
    }

    /**
     * addBillee 
     * Note how we only add a billee contact, if we don't already have it
     * 
     * @param Contact $contact 
     * @access public
     * @return void
     */
    public function addBillee(Contact $contact)
    {
        if (!$this->getBillees()->contains($contact)) {
            $this->billees[] = $contact;
        }
    }

    public function removeBillee(Contact $contact)
    {
        return $this->billees->removeElement($contact);
    }

    public function clearBillees()
    {
        foreach ($this->getBillees() as $ec) {
            $this->removeBillee($ec);
        }
    }



    public function getCurrentRank()
    {
        return $this->getRank();
    }

 
    /**
     * Get isProspective.
     *
     * @return isProspective.
     */
    public function getIsProspective()
    {
        return $this->isProspective;
    }
 
    /**
     * Set isProspective.
     *
     * @param isProspective the value to set.
     */
    public function setIsProspective($isProspective)
    {
        $this->isProspective = $isProspective;
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
        if (!$this->getLegacyStudentId()) {
            $this->setLegacyStudentId($now->format('Ymdhis'));
        }
    }

 
    /**
     * Get legacyStudentId.
     *
     * @return legacyStudentId.
     */
    public function getLegacyStudentId()
    {
        return $this->legacyStudentId;
    }
    
    /**
     * Set legacyStudentId.
     *
     * @param legacyStudentId the value to set.
     */
    public function setLegacyStudentId($legacyStudentId)
    {
        $this->legacyStudentId = $legacyStudentId;
        return $this;
    }
}
