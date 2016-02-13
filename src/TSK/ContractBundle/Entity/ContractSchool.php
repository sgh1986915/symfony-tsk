<?php

namespace TSK\ContractBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ContractSchool
 *
 * @ORM\Table(name="tsk_contract_school")
 * @ORM\Entity
 */
class ContractSchool
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contract_school_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    protected $contract;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\SchoolBundle\Entity\School", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_school_id", referencedColumnName="school_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     */
    protected $school;


    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(name="active_date", type="date")
     */
    private $active_date;


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
     * Set active_date
     *
     * @param \DateTime $activeDate
     * @return ContractSchool
     */
    public function setActiveDate($activeDate)
    {
        $this->active_date = $activeDate;
    
        return $this;
    }

    /**
     * Get active_date
     *
     * @return \DateTime 
     */
    public function getActiveDate()
    {
        return $this->active_date;
    }
 
    /**
     * Get contract.
     *
     * @return contract.
     */
    public function getContract()
    {
        return $this->contract;
    }
 
    /**
     * Set contract.
     *
     * @param contract the value to set.
     */
    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        return $this;
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
    public function setSchool(\TSK\SchoolBundle\Entity\School $school)
    {
        $this->school = $school;
        return $this;
    }
}
