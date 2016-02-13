<?php
namespace TSK\ContractBundle\Form\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContext;
use TSK\StudentBundle\Entity\Student;
use TSK\ContractBundle\Entity\Contract;
use TSK\ProgramBundle\Entity\Program;
use TSK\RulerBundle\Ruler\RulesEngineInterface;

/**
 * ContractUpgrade
 * 
 * @Assert\Callback(methods={"runDiscountEngine"}, groups={"flow_contractUpgrade_step1"})
 */
class ContractUpgrade
{
    protected $contract;
    protected $student;
    protected $program;
    protected $programPaymentPlan;
    protected $paymentPlanCustomizedPayments;
    protected $discountLevel;
    protected $trainingFamilyMember;
    protected $programExcludes;
    protected $programDiscountTypeFilters;
    protected $discountRuler;
    protected $programRuler;
 

    public function __construct(RulesEngineInterface $discountRuler, RulesEngineInterface $programRuler)
    {
        $this->programDiscountTypeFilters = new ArrayCollection();
        $this->programExcludes = new ArrayCollection();
        $this->programRuler = $programRuler;
        $this->discountRuler = $discountRuler;
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
     * Get program.
     *
     * @return program.
     */
    public function getProgram()
    {
        return $this->program;
    }
 
    /**
     * Set program.
     *
     * @param program the value to set.
     */
    public function setProgram(Program $program)
    {
        $this->program = $program;
    }
 
    /**
     * Get programPaymentPlan.
     *
     * @return programPaymentPlan.
     */
    public function getProgramPaymentPlan()
    {
        return $this->programPaymentPlan;
    }
 
    /**
     * Set programPaymentPlan.
     *
     * @param programPaymentPlan the value to set.
     */
    public function setProgramPaymentPlan($programPaymentPlan)
    {
        $this->programPaymentPlan = $programPaymentPlan;
        return $this;
    }
 
    /**
     * Get paymentPlanCustomizedPayments.
     *
     * @return paymentPlanCustomizedPayments.
     */
    public function getPaymentPlanCustomizedPayments()
    {
        return $this->paymentPlanCustomizedPayments;
    }
 
    /**
     * Set paymentPlanCustomizedPayments.
     *
     * @param paymentPlanCustomizedPayments the value to set.
     */
    public function setPaymentPlanCustomizedPayments($paymentPlanCustomizedPayments)
    {
        $this->paymentPlanCustomizedPayments = $paymentPlanCustomizedPayments;
        return $this;
    }
 
    /**
     * Get discountLevel.
     *
     * @return discountLevel.
     */
    public function getDiscountLevel()
    {
        return $this->discountLevel;
    }
 
    /**
     * Set discountLevel.
     *
     * @param discountLevel the value to set.
     */
    public function setDiscountLevel($discountLevel)
    {
        $this->discountLevel = $discountLevel;
        return $this;
    }
 
    /**
     * Get trainingFamilyMember.
     *
     * @return trainingFamilyMember.
     */
    public function getTrainingFamilyMember()
    {
        return $this->trainingFamilyMember;
    }
 
    /**
     * Set trainingFamilyMember.
     *
     * @param trainingFamilyMember the value to set.
     */
    public function setTrainingFamilyMember($trainingFamilyMember)
    {
        $this->trainingFamilyMember = $trainingFamilyMember;
        return $this;
    }

    /**
     * getProgramExcludes 
     * 
     * @access public
     * @return void
     */
    public function getProgramExcludes()
    {
        return $this->programExcludes;
    }

    /**
     * addProgramExclude 
     * 
     * @param mixed $programId 
     * @param int $value 
     * @access public
     * @return void
     */
    public function addProgramExclude($programId, $value=1)
    {
        $this->programExcludes->set($programId, $value);
    }


 
    /**
     * Get programDiscountTypeFilters.
     *
     * @return programDiscountTypeFilters.
     */
    public function getProgramDiscountTypeFilters()
    {
        return $this->programDiscountTypeFilters;
    }

    public function removeProgramExcludes($programId)
    {
        return $this->programDiscountTypeFilters->removeElement($programId);
    }

    
    /**
     * Set programDiscountTypeFilters.
     *
     * @param programDiscountTypeFilters the value to set.
     */
    // public function setProgramDiscountTypeFilters($programDiscountTypeFilters)
    // {
    //    foreach ($programDiscountTypeFilters as $idx => $pdtf) {
    //        $this->addProgramDiscountTypeFilters($pdtf);
    //    }
    // }

    public function addProgramDiscountTypeFilters($pdtf) 
    {
        if (!$this->programDiscountTypeFilters->contains($pdtf)) {
            $this->programDiscountTypeFilters[] = $pdtf; 
        }
    }

    public function setProgramDiscountTypeFilters($pdtf, $value=1)
    {
        $this->programDiscountTypeFilters->set($pdtf, $value);
    }

    public function removeProgramDiscountTypeFilters($pdtf)
    {
        return $this->programDiscountTypeFilters->removeElement($pdtf);
    }

    /**
     * getStudent 
     * 
     * @access public
     * @return Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    public function setStudent(Student $student)
    {
        $this->student = $student;
    }
    /**
     * validateFamilyDiscount 
     * This code is used to verify that a family member was selected
     * when using the family member discount.  If no family member was selected
     * then the discountLevel is set back to individual.  If a family was 
     * selected, then it queries to find out the level of the family member
     * (i.e. 1st, 2nd, 3rd)
     * @param ExecutionContext $context 
     * @access public
     * @return void
     */
    public function runDiscountEngine(ExecutionContext $context)
    {
        $this->discountRuler->applyRules('discount.pre', $this);
        return true;
    }

    public function getProgramPrincipal()
    {
        return $this->getProgramPaymentPlan()->getPrice();
        return 0;
        $customizedPaymentPlan = $this->getPaymentPlanCustomizedPayments();
        $json = $customizedPaymentPlan['paymentsData'];
        $obj = json_decode($json);
        return $obj->principal;
    }

    public function getAmountDue()
    {
        return $this->getProgramPrincipal() - $this->getContract()->getCreditDue();
    }
}
