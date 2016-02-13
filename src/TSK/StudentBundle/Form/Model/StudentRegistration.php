<?php
namespace TSK\StudentBundle\Form\Model;

use TSK\ProgramBundle\Entity\Program;
use TSK\ProgramBundle\Model\ProgramPaymentPlan;
use TSK\PaymentBundle\Entity\PaymentMethod;
use TSK\BilleeBundle\Entity\BilleePaymentMethod;
use TSK\SchoolBundle\Entity\School;
use TSK\BileeBundle\Entity\Billee;
use TSK\StudentBundle\Entity\Student;
use TSK\UserBundle\Entity\Contact;
use TSK\ContractBundle\Entity\Contract;
use TSK\UserBundle\Entity\EmergencyContact;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\ExecutionContextInterface;
use Symfony\Component\Validator\ExecutionContext;
use TSK\PaymentBundle\Ruler\DiscountRuler;

/**
 * StudentRegistration
 * 
 * @Assert\Callback(methods={"isBilleePaymentMethodValid"}, groups={"flow_registerStudent_step8"})
 * @Assert\Callback(methods={"runDiscountEngine"}, groups={"flow_registerStudent_step3"})
 * @Assert\Callback(methods={"runProgramEngine"}, groups={"flow_registerStudent_step3"})
 *
 */
class StudentRegistration
{
    private $school;
    private $program;
    private $programPaymentPlan;
    private $paymentPlanCustomizedPayments;

    private $student;
    private $contract;

    private $studentContact;
    private $billeeContact;
    private $emergencyContactContact;

    private $discountLevel;  // family or individual
    private $trainingFamilyMember; // primary training family member
    private $numTrainingFamilyMembers; // number of active training family members 
    private $nextAvailableTFMSlot; // next available family slot
    private $programDiscountTypeFilters; // hash of discount types that should be included
    private $programExcludes; // hash of programs that should be excluded

    private $copyFromStudent = false;
    private $copyStudentIntoEmerg = false;
    private $payInFull = false;

    private $paymentMethod;
    private $transArmorToken;
    private $billeePaymentMethod;
    private $ccNum;
    private $ccExpirationDate;
    private $cvvNumber;
    private $routingNumber;
    private $accountNumber;

    private $entityManager;
    private $discountRuler;
    private $programRuler;

    private $paymentGateway;

    private $contractBalanceDays;

    public function __construct($entityManager, $discountRuler, $programRuler, $paymentGateway)
    {
        $this->programDiscountTypeFilters = new ArrayCollection();
        $this->programExcludes = new ArrayCollection();
        $this->entityManager = $entityManager;
        $this->discountRuler = $discountRuler;
        $this->programRuler = $programRuler;
        $this->paymentGateway = $paymentGateway;
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
 public function setSchool($school)
 {
     $this->school = $school;
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
 public function setProgramPaymentPlan(ProgramPaymentPlan $programPaymentPlan)
 {
     $this->programPaymentPlan = $programPaymentPlan;
 }
 
 /**
  * Get copyFromStudent.
  *
  * @return copyFromStudent.
  */
 function getCopyFromStudent()
 {
     return $this->copyFromStudent;
 }
 
 /**
  * Set copyFromStudent.
  *
  * @param copyFromStudent the value to set.
  */
 public function setCopyFromStudent($copyFromStudent)
 {
     $this->copyFromStudent = $copyFromStudent;
 }
 
 /**
  * Get billeePaymentMethod.
  *
  * @return billeePaymentMethod.
  */
 public function getBilleePaymentMethod()
 {
     return $this->billeePaymentMethod;
 }
 
    /**
     * Set billeePaymentMethod.
     *
     * @param billeePaymentMethod the value to set.
     */
    public function setBilleePaymentMethod(PaymentMethod $billeePaymentMethod)
    {
        $this->billeePaymentMethod = $billeePaymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }
 /**
  * Get ccNum.
  *
  * @return ccNum.
  */
 public function getCcNum()
 {
     return $this->ccNum;
 }
 
 /**
  * Set ccNum.
  *
  * @param ccNum the value to set.
  */
 public function setCcNum($ccNum)
 {
     $this->ccNum = $ccNum;
 }
 
 /**
  * Get ccExpirationDate.
  *
  * @return ccExpirationDate.
  */
 public function getCcExpirationDate()
 {
     return $this->ccExpirationDate;
 }
 
 /**
  * Set ccExpirationDate.
  *
  * @param ccExpirationDate the value to set.
  */
 public function setCcExpirationDate($ccExpirationDate)
 {
     $this->ccExpirationDate = $ccExpirationDate;
 }
 
 /**
  * Get cvvNumber.
  *
  * @return cvvNumber.
  */
 public function getCvvNumber()
 {
     return $this->cvvNumber;
 }
 
 /**
  * Set cvvNumber.
  *
  * @param cvvNumber the value to set.
  */
 public function setCvvNumber($cvvNumber)
 {
     $this->cvvNumber = $cvvNumber;
 }
 
 /**
  * Get routingNumber.
  *
  * @return routingNumber.
  */
 public function getRoutingNumber()
 {
     return $this->routingNumber;
 }
 
 /**
  * Set routingNumber.
  *
  * @param routingNumber the value to set.
  */
 public function setRoutingNumber($routingNumber)
 {
     $this->routingNumber = $routingNumber;
 }
 
 /**
  * Get accountNumber.
  *
  * @return accountNumber.
  */
 public function getAccountNumber()
 {
     return $this->accountNumber;
 }
 
 /**
  * Set accountNumber.
  *
  * @param accountNumber the value to set.
  */
 public function setAccountNumber($accountNumber)
 {
     $this->accountNumber = $accountNumber;
 }
 
    public function runProgramEngine(ExecutionContext $context)
    {
        $this->programRuler->applyRules('program', $this);
    }

    /**
     * validateFamilyDiscount 
     * This function is used to verify that a family member was selected
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

    public function isBilleePaymentMethodValid(ExecutionContext $context)
    {
        // If user is not paying in full, then check cc/ach details
        if (!$this->getPayInFull()) {
            if ($this->getPaymentMethod()->getPaymentType() == 'CREDIT CARD') {
                if (!$this->getCcNum()) {
                    $context->addViolationAtPath('cc_num', 'Please add cc details');
                } else {
                    if (!$this->getCvvNumber()) {
                        $context->addViolationAtPath('cvv_number', 'Please add cvv numbers');
                    } else {
                        if (!$this->getCcExpirationDate()) {
                            $context->addViolationAtPath('cc_expiration_date', 'Please add cc exp date');
                        } else {
                            $today = new \DateTime();
                            if ($this->getCcExpirationDate() < $today) {
                                $context->addViolationAtPath('cc_expiration_date', 'Cc exp date must be in the future');
                            } else {
                                // now grab a pre-auth for the first payment
                                $pp = $this->getPaymentPlanCustomizedPayments();
                                $paymentObj = json_decode(stripslashes($pp['paymentsData']));
                                $payments = $paymentObj->payments;
                                $firstPayment = array_pop($payments);
                                $this->paymentGateway->setCardHoldersName((string) $this->getBilleeContact());
                                $this->paymentGateway->setCreditCardType($this->getPaymentMethod()->getName());
                                $this->paymentGateway->setCreditCardNumber($this->getCcNum());
                                $this->paymentGateway->setCreditCardExpiration($this->getCcExpirationDate());
                                $this->paymentGateway->setCreditCardVerification($this->getCvvNumber());
                                $this->paymentGateway->setCreditCardZipcode($this->getBilleeContact()->getPostalCode());
                                // $this->paymentGateway->setReferenceNumber(222);
                                try {
                                    $result = $this->paymentGateway->preAuth($firstPayment);
                                    $obj = json_decode($result);
                                    if (!$obj->transaction_error) {
                                        $this->setTransArmorToken($obj->transarmor_token);
                                    }
                                } catch (\Exception $e) {
                                    $context->addViolationAtPath('cc_num', $e->getMessage());
                                }
                                // ld($result);
                                // $obj = json_decode($result);
                                // $obj->transarmor_token;
                                // $obj->transaction_tag;
                                // $obj->retrieval_ref_no;
                                // $context->addViolationAtPath('cc_num', 'Bad cc num');
                            }
                        }
                    }

                }
            } else {
                if (!$this->getAccountNumber()) {
                    $context->addViolationAtPath('account_number', 'Please add account number');
                }
                if (!$this->getRoutingNumber()) {
                    $context->addViolationAtPath('routing_number', 'Please add routing number');
                }
            }
        }
    }

 
    /**
     * Get copyStudentIntoEmerg.
     *
     * @return copyStudentIntoEmerg.
     */
    public function getCopyStudentIntoEmerg()
    {
        return $this->copyStudentIntoEmerg;
    }
    
    /**
     * Set copyStudentIntoEmerg.
     *
     * @param copyStudentIntoEmerg the value to set.
     */
    public function setCopyStudentIntoEmerg($copyStudentIntoEmerg)
    {
        $this->copyStudentIntoEmerg = $copyStudentIntoEmerg;
    }
 
    /**
     * Get payInFull.
     *
     * @return payInFull.
     */
    public function getPayInFull()
    {
        return $this->payInFull;
    }
 
    /**
     * Set payInFull.
     *
     * @param payInFull the value to set.
     */
    public function setPayInFull($payInFull)
    {
        $this->payInFull = $payInFull;
        return $this;
    }

    /**
     * Get student.
     *
     * @return student.
     */
    public function getStudentContact()
    {
        return $this->studentContact;
    }
 
    /**
     * Set student.
     *
     * @param student the value to set.
     */
    public function setStudentContact(Contact $studentContact)
    {
        $this->studentContact = $studentContact;
        return $this;
    }
 
    /**
     * Get billee.
     *
     * @return billee.
     */
    public function getBilleeContact()
    {
        return $this->billeeContact;
    }
 
    /**
     * Set billee.
     *
     * @param billee the value to set.
     */
    public function setBilleeContact(Contact $billeeContact)
    {
        $this->billeeContact = $billeeContact;
        return $this;
    }
 
    /**
     * Get emergencyContactContact.
     *
     * @return emergencyContact.
     */
    public function getEmergencyContactContact()
    {
        return $this->emergencyContactContact;
    }
 
    /**
     * Set emergencyContactContact.
     *
     * @param emergencyContact the value to set.
     */
    public function setEmergencyContactContact(Contact $emergencyContactContact)
    {
        $this->emergencyContactContact = $emergencyContactContact;
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
 
    public function getNextAvailableTFMSlot()
    {
        return $this->nextAvailableTFMSlot;
    }

    public function setNextAvailableTFMSlot($slot)
    {
        $this->nextAvailableTFMSlot = $slot;
        return $this;
    }

    /**
     * Get discountNumTrainingFamilyMembers.
     *
     * @return discountNumTrainingFamilyMembers.
     */
    public function getNumTrainingFamilyMembers()
    {
        return $this->numTrainingFamilyMembers;
    }
 
    /**
     * Set discountNumTrainingFamilyMembers.
     *
     * @param discountNumTrainingFamilyMembers the value to set.
     */
    public function setNumTrainingFamilyMembers($numTrainingFamilyMembers)
    {
        $this->numTrainingFamilyMembers = $numTrainingFamilyMembers;
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
    }

    public function getProgramExcludes()
    {
        return $this->programExcludes;
    }

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

    public function getContract()
    {
        return $this->contract;
    }

    public function setContract(Contract $contract)
    {
        $this->contract = $contract;
        return $this;
    }

    public function loadContractDetails()
    {
        // set student contact
        foreach ($this->getContract()->getStudents() as $student) {
            $this->setStudentContact($student->getContact());
            $this->setStudent($student);
            // set emergency contact
            foreach ($student->getEmergencyContacts() as $ec) {
                $this->setEmergencyContactContact($ec);
            }
        }

        // how do we load program?
        // look for program by name, in same org and active
        $program = $this->entityManager->getRepository('TSK\ProgramBundle\Entity\Program')->findOneBy(array('organization' => $this->getContract()->getOrganization(), 'programName' => $this->getContract()->getProgram()->getProgramName(), 'isActive' => true));
        if ($program) {
            $this->setProgram($program);
        }

        // how do we load program payment plan
        // to get deferral info?
        // instantiate a new ProgramPaymentPlan model and populate with vars from contract
        $programPaymentPlan = new ProgramPaymentPlan();
        $programPaymentPlan->setDeferralRate($this->getContract()->getDeferralRate());
        $programPaymentPlan->setDeferralDistributionStrategy($this->getContract()->getDeferralDistributionStrategy());
        $programPaymentPlan->setDeferralDurationMonths($this->getContract()->getDeferralDurationMonths());
        $this->setProgramPaymentPlan($programPaymentPlan);

        // how do we load payments?
        $this->setPaymentPlanCustomizedPayments($this->getContract()->getPaymentTerms());

        // how do we load billee contact
        $bpmContracts = $this->entityManager->getRepository('TSK\BilleeBundle\Entity\BilleePaymentMethodContract')->findBy(array('contract' => $this->getContract()));
        foreach ($bpmContracts as $bpmContract) {
            $this->setBilleeContact($bpmContract->getBilleePaymentMethod()->getContact());
            $this->setBilleePaymentMethod($bpmContract->getBilleePaymentMethod()->getPaymentMethod());
            $this->setRoutingNumber($bpmContract->getBilleePaymentMethod()->getRoutingNum());
            $this->setAccountNumber($bpmContract->getBilleePaymentMethod()->getAccountNum());
            $this->setCvvNumber($bpmContract->getBilleePaymentMethod()->getCvvNumber());
            $this->setCcExpirationDate($bpmContract->getBilleePaymentMethod()->getCcExpirationDate());
            $this->setCcNum($bpmContract->getBilleePaymentMethod()->getCcNum());
        }

        // load contract balance
        $this->setContractBalanceDays($this->getContract()->getBalanceInDays());
        
    }
 
    /**
     * Get contractBalanceDays.
     *
     * @return contractBalanceDays.
     */
    function getContractBalanceDays()
    {
        return $this->contractBalanceDays;
    }
    
    /**
     * Set contractBalanceDays.
     *
     * @param contractBalanceDays the value to set.
     */
    function setContractBalanceDays($contractBalanceDays)
    {
        $this->contractBalanceDays = $contractBalanceDays;
        return $this;
    }

    public function getTransArmorToken()
    {
        return $this->transArmorToken;
    }

    public function setTransArmorToken($transArmorToken)
    {
        $this->transArmorToken = $transArmorToken;
    }
}
