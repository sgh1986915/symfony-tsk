<?php
namespace TSK\ContractBundle\Form\Model;
use TSK\StudentBundle\Entity\Student;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContext;
use TSK\ContractBundle\Entity\Contract;

/**
 * @Assert\Callback(methods={"validate"})
 */
class ContractFreeze
{
    protected $contract;
    protected $student;
    protected $startDate;
    protected $endDate;
 
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
    public function setStudent(Student $student)
    {
        $this->student = $student;
        return $this;
    }
 
    /**
     * Get startDate.
     *
     * @return startDate.
     */
    public function getStartDate()
    {
        return $this->startDate;
    }
 
    /**
     * Set startDate.
     *
     * @param startDate the value to set.
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }
 
    /**
     * Get endDate.
     *
     * @return endDate.
     */
    public function getEndDate()
    {
        return $this->endDate;
    }
 
    /**
     * Set endDate.
     *
     * @param endDate the value to set.
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        return $this;
    }

    /**
     */
    public function validate(ExecutionContextInterface $context)
    {
        if ($this->getStartDate() >= $this->getEndDate()) {
            $context->addViolationAtPath('startDate', 'Start Date must be less than end date');
        }

        $today = new \DateTime();
        if ($this->getStartDate() < $today) {
            $context->addViolationAtPath('startDate', 'Start Date cannot be in the past.');
        }
    }

    public function getTotalFreezeDays()
    {
        return $this->getStartDate()->diff($this->getEndDate())->format('%R%a');
    }

}
