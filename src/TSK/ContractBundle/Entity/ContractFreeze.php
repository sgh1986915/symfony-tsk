<?php

namespace TSK\ContractBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContext;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContractFreeze
 *
 * @Assert\Callback(methods={"validate"})
 * @ORM\Table(name="tsk_contract_freeze")
 * @ORM\Entity(repositoryClass="TSK\ContractBundle\Entity\ContractFreezeRepository")
 */
class ContractFreeze
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", inversedBy="freezes")
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false)
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    private $contract;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\StudentBundle\Entity\Student")
     * @ORM\JoinColumn(name="fk_student_id", referencedColumnName="student_id", nullable=false)
     * @Assert\Type(type="TSK\StudentBundle\Entity\Student")
     */
    private $student;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date")
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date")
     */
    private $endDate;


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
     * Set startDate
     *
     * @param \DateTime $startDate
     * @return ContractFreeze
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     * @return ContractFreeze
     */
    public function setEndDate(\DateTime $endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime 
     */
    public function getEndDate()
    {
        return $this->endDate;
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
    public function setContract(\TSK\ContractBundle\Entity\Contract $contract)
    {
        $this->contract = $contract;
        return $this;
    }
 
    /**
     * Get student.
     *
     * @return student.
     */
    public function getStudent()
    {
        return $this->student;
    }
 
    /**
     * Set student.
     *
     * @param student the value to set.
     */
    public function setStudent(\TSK\StudentBundle\Entity\Student $student)
    {
        $this->student = $student;
        return $this;
    }

    public function getFreezeDays()
    {
        return $this->getStartDate()->diff($this->getEndDate())->format('%R%a');
    }

    /**
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getStartDate() >= $this->getEndDate()) {
            $context->addViolationAtPath('startDate', 'Start Date must be less than end date');
        }
    }
}
