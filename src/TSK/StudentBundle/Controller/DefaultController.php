<?php

namespace TSK\StudentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\DBALException;
// use Sonata\AdminBundle\Controller\CRUDController;
use TSK\StudentBundle\Form\Model\StudentRegistration;
use TSK\UserBundle\Entity\Contact;
use TSK\UserBundle\Entity\EmergencyContact;
use TSK\StudentBundle\Entity\Student;
use TSK\ContractBundle\Entity\ContractToken;
use TSK\BilleeBundle\Entity\Billee;
use TSK\BilleeBundle\Entity\BilleePaymentMethod;
use TSK\BilleeBundle\Entity\BilleePaymentMethodContract;
use TSK\ContractBundle\Entity\Contract;
use TSK\PaymentBundle\Entity\Charge;
use TSK\RecurringPaymentBundle\Entity\RecurringPaymentInstruction;
use TSK\PaymentBundle\Util\Dates as DatesUtil;
use TSK\StudentBundle\Entity\StudentRank;
use TSK\RankBundle\Entity\Rank;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

use TSK\StudentBundle\Event\StudentEvents;
use TSK\StudentBundle\Event\StudentPostRegistrationEvent;
use TSK\StudentBundle\Event\StudentProgressEvent;
use TSK\StudentBundle\Form\Type\StudentPromotionFormType;
use TSK\ContractBundle\Event\ContractEvents;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class DefaultController extends Controller
{
    /**
     * @Route("/studentdata")
     * @Template()
     */
    public function indexAction()
    {
        $flow = $this->get('tsk_student.form.flow.registerStudent'); 
        $studentData = $this->get('session')->get($flow->getStepDataKey());
        $step4Data = (!empty($studentData[4])) ? $studentData[4] : NULL;

        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($step4Data));
        $response->headers->set('Content-Type', 'application/json');
        return $response;

        // return array('curstep' => json_encode($studentData));
    }

    /**
     * 
     * @Route("/saveprospective", name="tsk_student_save_prospective")
     * @PreAuthorize("hasRole('ROLE_ADMIN')")
     */
    public function saveProspectiveAction()
    {
        $discountRuler = $this->get('tsk_payment.ruler.discount_rules_engine');
        $programRuler = $this->get('tsk_program.ruler.program_rules_engine');
        $paymentGateway = $this->get('tsk_payment.gateway.service');
        $em = $this->getDoctrine()->getManager();
        $studentRegistration = new StudentRegistration($em, $discountRuler, $programRuler, $paymentGateway); 
        // set the school right here from session
        $session = $this->getRequest()->getSession();
        $school_id = $session->get($this->container->getParameter('tsk_user.session.school_key'));

        $school = $em->getRepository('TSK\SchoolBundle\Entity\School')->findOneBy(array('id' => $school_id));
        if (!$school) {
            throw new \Exception('Invalid school stored in session');
        }
        $studentRegistration->setSchool($school);


        $flow = $this->get('tsk_student.form.flow.registerStudent'); // must match the flow's service id
        $flow->bind($studentRegistration);

        // Create prospective student
        $studentContact = $studentRegistration->getStudentContact();
        $contactRepo = $em->getRepository('TSK\UserBundle\Entity\Contact');
        if ($dupeContact = $contactRepo->findDupe($studentContact)) {
            $studentContact = $dupeContact;
        }
        $studentContact->addSchool($studentRegistration->getSchool());
        $student = new Student();
        $student->setContact($studentContact);
        $student->setIsProspective(true);

        try {
            $em->persist($student);
            $em->flush();
            $session->getFlashBag()->add('success', 'Prospective Student Saved!!');
        } catch (DBALException $e) {
            $session->getFlashBag()->add('info', 'Contact already exists in students table');
        }

        $flow->reset();
        return $this->redirect($this->generateUrl('tsk_student_default_registerstudent')); // redirect when done
    }

    /**
     * @Route("/register")
     * @Route("/register/")
     * @Route("/register/contract/upgrade/{contractId}", name="tsk_student_contract_upgrade")
     * @Route("/register/contract/renew/{contractId}", name="tsk_student_contract_renew")
     * @Template()
     * @PreAuthorize("hasRole('ROLE_ADMIN')") 
     */
    public function registerStudentAction($contractId=null)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->container->get('request');
        $routeName = $request->get('_route');
        $discountRuler = $this->get('tsk_payment.ruler.discount_rules_engine');
        $programRuler = $this->get('tsk_program.ruler.program_rules_engine');
        $paymentGateway = $this->get('tsk_payment.gateway.service');
        $studentRegistration = new StudentRegistration($em, $discountRuler, $programRuler, $paymentGateway); 
        // set the school right here from session
        $session = $this->getRequest()->getSession();
        $school_id = $session->get($this->container->getParameter('tsk_user.session.school_key'));

        $school = $em->getRepository('TSK\SchoolBundle\Entity\School')->findOneBy(array('id' => $school_id));
        if (!$school) {
            throw new \Exception('Invalid school stored in session');
        }
        $studentRegistration->setSchool($school);

        switch ($routeName) {
            case 'tsk_student_contract_renew':
            case 'tsk_student_contract_upgrade':
                $action = 'renew';
                $contract = $em->getRepository('TSK\ContractBundle\Entity\Contract')->findOneBy(array('id' => $contractId));
                if (!$contract) {
                    throw new \Exception('Contract not found');
                }
                if (!$contract->getIsActive()) {
                    throw new \Exception('Contract found, but is not active');
                }
                if ($contract->getSchool() != $school) {
                    throw new \Exception('Access Denied');
                }
                $studentRegistration->setContract($contract);
                $studentRegistration->loadContractDetails();
            
                //$this->loadRegistrationFromContract($contract, $studentRegistration);
                // $this->saveRenewedContract();
            break;

            case 'xtsk_student_contract_upgrade':
                $action = 'upgrade';
                // $this->loadRegistrationFromContract($contract, $studentRegistration);
                // $this->saveUpgradedContract();
            break;

            default:
                $action = 'register';
                // $this->saveNewContract();
            break;
        }

        $flow = $this->get('tsk_student.form.flow.registerStudent'); // must match the flow's service id
        $flow->bind($studentRegistration);

        // form of the current step
        $form = $flow->createForm($studentRegistration);
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData();

            if ($flow->nextStep()) {
                // form for the next step
                $form = $flow->createForm($studentRegistration);
            } else {

                switch ($routeName) {
                    case 'tsk_student_contract_renew':
                        $this->saveRenewedContract($em, $studentRegistration);
                    break;

                    case 'tsk_student_contract_upgrade':
                        print '<h2>wowowow</h2>';
                        print '<h2>wowowow</h2>';
                        // $this->saveUpgradedContract($em, $oldContractId, $studentRegistration);
                    break;

                    default:
                        $this->saveNewStudentRegistration($em, $studentRegistration);
                    break;
                }

                $session = $this->get('session');
                $session->getFlashBag()->add('success', 'Student Registered!!');
                $flow->reset();

               return $this->redirect($this->generateUrl('tsk_student_default_registerstudent')); // redirect when done
            }
        }

        $ok = $this->render('TSKStudentBundle:Default:registerStudent.html.twig', array(
                'form' => $form->createView(),
                'flow' => $flow,
                ));
        return $ok;
    }

    /**
     * @Route("/promote/{id}")
     * @Template()
     * @Method({"GET","POST"})
     */
    public function promoteAction(Student $student)
    {
        $eligibleRanks = $student->getEligibleRanks();
        $forms = array();
        foreach ($eligibleRanks as $rank) {
            $studentRank = new StudentRank();
            $studentRank->setStudent($student);
            $studentRank->setRank($rank);
            $studentRank->setRankType($rank->getRankType());
            $form = $this->createForm(new StudentPromotionFormType(), $studentRank);
            $forms[] = $form->createView();
        }

        $request = $this->get('request');
        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                if ($studentRank->getRank() == $student->getRank()) {
                    $this->get('session')->getFlashBag()->add('error', 'ERROR:  Cannot promote student to same rank');
                } else {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($studentRank);
                    $em->flush();

                    // Dispatch student progress event ...
                    $studentProgressEvent = new StudentProgressEvent($student);
                    $dispatcher = $this->get('event_dispatcher');
                    $dispatcher->dispatch(StudentEvents::STUDENT_PROGRESS, $studentProgressEvent);
                    $this->get('session')->getFlashBag()->add('success', 'Student Promoted!');
                    // Force a redirect here to get the updated data
                    return $this->redirect($this->getRequest()->headers->get('referer'));
                }
            }
        }
        return array('student' => $student, 'forms' => $forms);
    }

    /**
     * @Route("/crest")
     * @Template()
     * @Method({"GET"})
     */
    public function crestAction()
    {
        $ctx = new \Ruler\Context(array());
        $em = $this->getDoctrine()->getManager();
        $class = new \TSK\RankBundle\Ruler\RankReward($em, $ctx);
        $ref = new \ReflectionClass('\TSK\RankBundle\Ruler\RankReward');
        print $ref->getDocComment();
        ld($ref->getMethods());
        print 'howdy';
        return array('foo' => 'bar');
    }

    /**
     * @Route("/fest/{id}")
     * @Template()
     * @Method({"GET"})
     */
    public function festAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        // inject context?
        $discountRulesEngine = $this->get('tsk_payment.ruler.discount_rules_engine');
        $programRulesEngine = $this->get('tsk_program.ruler.program_rules_engine');
        $paymentGateway = $this->get('tsk_payment.gateway.service');
        $studentRegistration = new StudentRegistration($em, $discountRulesEngine, $programRulesEngine, $paymentGateway);
        $studentRegistration->setStudent($student);
        // $program = $em->getRepository('TSK\ProgramBundle\Entity\Program')->find(1);
        // $studentRegistration->setProgram($program);
        if ($programRulesEngine->applyRules('program.post', $studentRegistration)) {
            print 'we passed!';
        } else {
            print 'we failed';
        }
        return array('foo' => 'bar');
    }

    /**
     * @Route("/test/")
     * @Template()
     * @Method({"GET"})
     */
    public function testAction()
    {
/*
        $em = $this->getDoctrine()->getManager();
        $kbClassType = $em->getRepository('TSK\ClassBundle\Entity\ClassType')->findOneBy(array('name' => 'KICKBOXING'));
        // $promoDate = $em->getRepository('TSK\StudentBundle\Entity\StudentRank')->getLastPromotionDate($student);
        // $numStripes = $em->getRepository('TSK\StudentBundle\Entity\StudentRank')->getNumberStripesEarnedAtCurrentBelt($student);
        $numCreditsEarned = $em->getRepository('TSK\StudentBundle\Entity\StudentCreditLog')->getCreditsEarnedSinceLastPromotion($student, $kbClassType);
        ld($numCreditsEarned);
        return array('student' => $student);

        $em = $this->getDoctrine()->getManager();
        $student->clearEligibleRanks();
        $student->addEligibleRank($rank);
        // $student->setRank($rank);
        $em->persist($student);
        $em->flush();
*/
        $em = $this->getDoctrine()->getManager();
        $dr = new \TSK\PaymentBundle\Ruler\DiscountRuler($em, $this->get('session'), 'tsk_organization_id');

        $program = $em->getRepository('TSK\ProgramBundle\Entity\Program')->find(12);
        $studentRegistration = new StudentRegistration($em);
        $studentRegistration->setProgram($program);
        // $dr->applyRules($studentRegistration, 'discount.pre');
        return array('student' => 'student', 'rank' => 'rank');
    }

    public function saveNewStudentRegistration($em, $studentRegistration)
    {
        $contactRepo = $em->getRepository('TSK\UserBundle\Entity\Contact');
        $studentRepo = $em->getRepository('TSK\StudentBundle\Entity\Student');
        // flow finished
        $student = $studentRegistration->getStudent();
        if (!$student) {
            $studentContact = $studentRegistration->getStudentContact();
            // check for dupe student
            if ($dupeStudent = $studentRepo->findOneBy(array('contact' => $studentContact))) {
                $student = $dupeStudent;
                $student->getContact()->addSchool($studentRegistration->getSchool());
            } else {
                // Create student
                // check for dupe contact
                if ($dupeContact = $contactRepo->findDupe($studentContact)) {
                    $studentContact = $dupeContact;
                }
                $studentContact->addSchool($studentRegistration->getSchool());
                $student = new Student();
                $student->setContact($studentContact);
            }
        }
        $studentStatusActive = $em->getRepository('TSK\StudentBundle\Entity\StudentStatus')->findOneBy(array('name' => 'active', 'organization' => $this->getOrg()));
        $student->setStudentStatus($studentStatusActive);
        $student->setIsProspective(false);

        // Create Billee
        $billeeContact = $studentRegistration->getBilleeContact();
        if ($dupeContact = $contactRepo->findDupe($billeeContact)) {
            $billeeContact = $dupeContact;
        }
        $billeeContact->addSchool($studentRegistration->getSchool());
        $student->addBillee($billeeContact);

        // Create Emergency Contact
        $emergencyContact = $studentRegistration->getEmergencyContactContact();
        if ($dupeContact = $contactRepo->findDupe($emergencyContact)) {
            $emergencyContact = $dupeContact;
        }
        $emergencyContact->addSchool($studentRegistration->getSchool());
        $student->addEmergencyContact($emergencyContact);

        $pp = $studentRegistration->getPaymentPlanCustomizedPayments();
        // Hmmm ... after adding Gedmo loggable now I need to stripslashes on json data ...
        $paymentObj = json_decode(stripslashes($pp['paymentsData']));

        // Create contract
        // Soon we will need to select the contract based on membership type
        $contract = new Contract();
        $contract->setIsActive(true);
        $contract->setProgram($studentRegistration->getProgram());
        // $contract->setAmount($paymentObj->principal);
        $contract->setSchool($studentRegistration->getSchool());
        $contract->addStudent($student);
        $contract->setPaymentTerms($studentRegistration->getPaymentPlanCustomizedPayments());

        $contractStartDate = new \DateTime(); // We may want the ability to set this manually in the future ...
        $contract->setContractStartDate($contractStartDate);
        $programExpiry = new \DateTime();
        $expireDays = $studentRegistration->getProgram()->getDurationDays() + $studentRegistration->getContractBalanceDays();
        $programExpiry->add(new \DateInterval('P' . $expireDays . 'D'));
        $contract->setContractExpiry($programExpiry);
        $contract->setContractNumTokens($studentRegistration->getProgram()->getNumTokens());
        $contract->setRolloverDays($studentRegistration->getContractBalanceDays());
        $contract->setDeferralRate($studentRegistration->getProgramPaymentPlan()->getDeferralRate());
        $contract->setDeferralDurationMonths($studentRegistration->getProgramPaymentPlan()->getDeferralDurationMonths());
        $contract->setDeferralDistributionStrategy($studentRegistration->getProgramPaymentPlan()->getDeferralDistributionStrategy());

        // Add to contract_token table

        $contractToken = new ContractToken();
        $contractToken->setContract($contract);
        $contractToken->setAmount($studentRegistration->getProgram()->getNumTokens());
        // Billee Payment Method
        if (!$studentRegistration->getPayInFull()) {
            $bpm = new BilleePaymentMethod();
            $bpm->setContact($billeeContact);
            $bpm->setPaymentMethod($studentRegistration->getPaymentMethod());
            // $bpm->setCcNum($studentRegistration->getCcNum());
            $bpm->setTransArmorToken($studentRegistration->getTransArmorToken());
            if ($studentRegistration->getCcExpirationDate()) {
                $bpm->setCcExpirationDate($studentRegistration->getCcExpirationDate());
            }
            $bpm->setCvvNumber($studentRegistration->getCvvNumber());
            // $bpm->setRoutingNum($studentRegistration->getRoutingNumber());
            // $bpm->setAccountNum($studentRegistration->getAccountNumber());
        }

        if (!empty($bpm)) {
            $bpmContract = new BilleePaymentMethodContract();
            $bpmContract->setContract($contract);
            $bpmContract->setBilleePaymentMethod($bpm);
            $bpmContract->setPortion(100);
        }

        // Set up charges 
        $session = $this->getRequest()->getSession();
        $sessionKey = $this->container->getParameter('tsk_user.session.org_key');
        $org = $this->get('session')->get($sessionKey);
        $tuitionIncomeType = $em->getRepository('TSK\PaymentBundle\Entity\IncomeType')->findOneBy(array('name' => 'tuition', 'organization' => $org));
        // Grab IncFmStudents account
        $incFmStudents = $em->getRepository('TSK\PaymentBundle\Entity\Account')->findOneBy(array('name' => 'Inc Fm Students', 'organization' => $org));
        $deferralAccount = $em->getRepository('TSK\PaymentBundle\Entity\Account')->findOneBy(array('name' => 'Deferred Income', 'organization' => $org));
        if (!empty($paymentObj->payments)) {
            $payments = $paymentObj->payments;
            foreach ($payments as $idx => $payment) {
                $charge = new Charge();
                $charge->setSchool($studentRegistration->getSchool());
                $charge->setAmount($payment);
                $charge->setAccount($incFmStudents);
                $charge->setDeferralAccount($deferralAccount);
                $charge->setIncomeType($tuitionIncomeType);

                // 0 Months, 1 month, 2 months ...
                // TODO:  Make this respond to paymentFrequency

                $du = new DatesUtil();
                $today = new \DateTime();
                $charge->setDueDate($du->getMonthaversary($today->format('Y-m-d'), $idx));
                $em->persist($charge);
                // It feels a little funny doing this here, could be improved ... MJH
                $contract->addCharge($charge);
            }
        }

        $em->persist($student);
        $em->persist($billeeContact);
        $em->persist($emergencyContact);
        $em->persist($contract);
        $em->persist($contractToken);
        if (!empty($bpm)) {
            $em->persist($bpm);
            $em->persist($bpmContract);
        }

        // Set up RPI's
        if (!empty($bpm)) {
            if (!empty($paymentObj->payments)) {
                $payments = $paymentObj->payments;
                foreach ($payments as $idx => $payment) {
                    $rpi = new RecurringPaymentInstruction();
                    $rpi->setAmount($payment);
                    $rpi->setBilleePaymentMethod($bpm);
                    $rpi->setContract($contract);
                    $rpi->setStatus('pending');

                    // 0 Months, 1 month, 2 months ...
                    // TODO:  Make this respond to paymentFrequency
                    $du = new DatesUtil();
                    $today = new \DateTime();
                    $rpiDueDate = $du->getMonthaversary($today->format('Y-m-d'), $idx);
                    $rpi->setRunDate($rpiDueDate);
                    $em->persist($rpi);
                }
            }
        }

        $em->flush();

        // So we actually need to trigger a post registration event
        // We also need to run our discount.post rules engine

        $studentRegistration->setStudent($student);
        $studentRegistration->setContract($contract);
        $studentPostRegistrationEvent = new StudentPostRegistrationEvent($student, $studentRegistration);
        $dispatcher = $this->get('event_dispatcher');
        $dispatcher->dispatch(StudentEvents::STUDENT_REGISTRATION_POST, $studentPostRegistrationEvent);
    }

    public function saveRenewedContract($em, $studentRegistration)
    {

        // immediately cancel existing contract
        $this->cancelContract($em, $studentRegistration);
        $this->saveNewStudentRegistration($em, $studentRegistration);
        // update any contact info
        // Create student
        // $studentContact = $studentRegistration->getStudentContact();
        // $studentContact->addSchool($studentRegistration->getSchool());
        // $student = new Student();
        // $student->setContact($studentContact);

        // update any payment method info
        // create new contract w/ new terms
        // rollover????
        // set up charges
        // set up recurring payment instructions
    }

    public function cancelContract($em, $studentRegistration)
    {
        $student = $studentRegistration->getStudent();
        if (!$student) {
            throw new \Exception('Invalid student');
        }
        $currentContract = $student->getActiveContract();
        if (!$currentContract) {
            throw new \Exception('No active contract to cancel');
        }
        // $currentContract->setIsActive(0);
        // $em = $this->getDoctrine()->getManager();
        // $em->persist($currentContract);
        // $em->flush();
        
        $event = new \TSK\ContractBundle\Event\ContractEvent($currentContract);
        $dispatcher = $this->container->get('event_dispatcher');
        $dispatcher->dispatch(ContractEvents::CANCEL, $event);
    }

    public function saveUpgradedContract($em, $oldContractId, $studentRegistration)

    {
    }

    private function getOrg()
    {
        $session = $this->getRequest()->getSession();
        $org_id = $session->get($this->container->getParameter('tsk_user.session.org_key'));

        $em = $this->getDoctrine()->getManager();
        $org = $em->getRepository('TSK\UserBundle\Entity\Organization')->findOneBy(array('id' => $org_id));
        if (!$org) {
            throw new \Exception('Invalid org stored in session');
        }
        return $org;
    }
}
