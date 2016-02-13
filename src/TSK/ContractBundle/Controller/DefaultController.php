<?php

namespace TSK\ContractBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Ps\PdfBundle\Annotation\Pdf;
use TSK\StudentBundle\Entity\Student;
use TSK\ContractBundle\Util\String;
use TSK\ContractBundle\Entity\Contract;
use TSK\ContractBundle\Event\ContractEvents;
use TSK\ContractBundle\Form\Model\ContractModifier;
use TSK\ContractBundle\Entity\ContractTemplate;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityRepository;
use TSK\ContractBundle\Form\Model\ContractUpgrade;
use TSK\ContractBundle\Form\Type\UpgradeContractType;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/contract/variables")
     * @Template()
     */
    public function variablesAction()
    {
        return array('foo' => 'bar');
    }

    /**
     * @Route("/admin/contract/{id}", defaults={"_format" = "pdf" })
     */
    public function indexAction(Student $student)
    {
        // Get contract by studentId or by contactId
        $contracts = $student->getContracts();
        $currentExpiry = NULL;
        foreach ($contracts as $contract) {
            if ($contract->getContractExpiry() > $currentExpiry) {
                $currentExpiry = $contract->getContractExpiry();
                $latestContract = $contract;
            }
        }

        $s = new String();
        $paymentTerms = $latestContract->getPaymentTerms();
        ld($paymentTerms); exit;
        $installments = $s->stringifyPayments($latestContract->getChargesAsArray());

        if (!$latestContract) {
            throw new \Exception('No contract for student');
        }
        

        // Get contract school and school state
        $school = $latestContract->getSchool();
        $cts = $latestContract->getMembershipType()->getContractTemplates();

        // ld($latestContract->getMembershipType());
        $contractTemplate = $cts->last();
        if ($contractTemplate) {
            $template = $contractTemplate->getTemplate();

            $org = $this->session->get('tsk_organization_id');

            $facade = $this->get('ps_pdf.facade');
            $response = new Response();
            $stringRenderer = $this->get('tsk.twig.string');
            $output = $stringRenderer->render($template, array(
                'contract' => $latestContract, 
                'school' => $school, 
                'org' => $org,
                'blackBeltFee' => 495,  // config var
                'student' => $student, 
                'installments' => '$installments')
            );

            // $xml = $response->getContent();
            $content = $facade->render($output);
            return new Response($content, 200, array('content-type' => 'application/pdf'));
            return array('of');
            exit;
        }
        
        $refundPolicy = $this->generateRefundPolicy($student->getContracts()->count(), $school->getContact()->getState()->getStateName(), $latestContract->getAmount());
        $cancelPolicy = $this->generateCancelPolicy($school->getContact()->getState()->getStateName());
        $format = $this->get('request')->get('_format');
        return $this->render('TSKContractBundle:Default:index.pdf.twig', 
            array(
                'contract' => $latestContract, 
                'school' => $school, 
                'orgName' => "Tiger Schulmann's Mixed Martial Arts Center", 
                'abbrOrgName' => 'TSMMA',
                'blackBeltFee' => 495,
                'student' => $student, 
                'refundPolicy' => $refundPolicy,
                'cancelPolicy' => $cancelPolicy,
                'installments' => $installments)
        );
    }

    public function generateCancelPolicy($schoolState)
    {
        $cancelPolicy = '';
        if ($schoolState == 'CT') {
            $em = $this->getDoctrine()->getManager();
            $cancelPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'CT CANCEL 1'))->getContent();
            $cancelPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'CT CANCEL 2'))->getContent();
        }
        return $cancelPolicy;
    }

    public function generateRefundPolicy($numAgreements, $schoolState, $programCost)
    {
        $em = $this->getDoctrine()->getManager();
        $refundPolicy = '';
        if ($programCost) {
            switch ($schoolState) {
                case 'CT':
                    $refundPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'CT REFUND 1'))->getContent();
                    if ($numAgreements < 2) {
                        $refundPolicy .= '  ' . $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'CT REFUND 2'))->getContent();
                    }
                    $refundPolicy .= '  ' . $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'CT REFUND 3'))->getContent();
                break;

                default:
                    $refundPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'DEFAULT REFUND 1'))->getContent();
                    if ($schoolState == 'NY') {
                        $refundPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'NY REFUND 1'))->getContent();
                    } else {
                        $refundPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'DEFAULT REFUND 2'))->getContent();
                    }
                    $refundPolicy .= $em->getRepository('TSKContractBundle:ContractClause')->findOneBy(array('name' => 'DEFAULT REFUND 3'))->getContent();
                    
                break;
            }
        }
        return $refundPolicy; 
    }

    /**
     * testActionfos_user_profile_show 
     * 
     * @Route("/admin/testcontract/{id}")
     * @Template()
     * 
     * @access public
     * @return void
     */
    public function testAction(Student $student)
    {

        print $this->get('tsk.twig.string')->render('Hello {{ name }}', array('name' => 'Fabien'));
        $currentExpiry = NULL;
        $contracts = $student->getContracts();
        foreach ($contracts as $contract) {
            if ($contract->getContractExpiry() > $currentExpiry) {
                $currentExpiry = $contract->getContractExpiry();
                $latestContract = $contract;
            }
        }
        $chgs = array();
        foreach ($latestContract->getCharges() as $charge) {
            $chgs[] = $charge->getAmount();
        }
        if (count($chgs)) {
            $s = new String();
            $installments = $s->stringifyPayments(array(500, 500));
        }
        return array('student' => $student, 'installments' => $installments);
    }

    /**
     * @Route("/admin/contract_template/get_version/{object_id}/{version}", name="tsk_contract_template_version", options={"expose"=true})
     * @Template()
     */
    public function getVersionAction($object_id, $version)
    {
        $em = $this->getDoctrine()->getManager();
        $v = $em->getRepository('TSKContractBundle:ContractTemplateVersion')->findOneBy(array('objectId' => $object_id, 'version' => $version));
        $data = $v->getData();

        $response = new JsonResponse();
        $response->setData($data);
        return $response;
        // return array('data' => $data);
    }

    /**
     * @Route("/admin/contract/renew/{id}", name="tsk_contract_renew")
     * @Template()
     */
    public function renewAction(Student $student)
    {
        $contractModifier = new ContractModifier();
        $contract = $student->getActiveContract();
        if (!$contract) {
            throw new \Exception('No active contract for student');
        }
        $contractModifier->setContract($contract);
        $flow = $this->get('tsk_contract.form.flow.contractModifier');
        $flow->bind($contractModifier);


        $form = $flow->createForm($contractModifier);
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData();

            if ($flow->nextStep()) {
                $form = $flow->createForm($contractModifier);
            } else {
            }
        }

        return array('form' => $form->createView(), 'flow' => $flow);
    }

    /**
     * @Route("/admin/contract/upgrade/{id}", name="tsk_contract_upgrade")
     * @Template()
     */
    public function upgradeAction(Contract $contract)
    {
        $discountRuler = $this->get('tsk_payment.ruler.discount_rules_engine');
        $programRuler = $this->get('tsk_program.ruler.program_rules_engine');
        $request = $this->container->get('request');
        $session = $this->getRequest()->getSession();
        $contractUpgrade = new ContractUpgrade($discountRuler, $programRuler);
        if (!$contract->getIsActive()) {
            throw new \Exception('Contract is not active, cannot upgrade');
        }
        $contractUpgrade->setContract($contract);
        $student = $contract->getStudents()->first();
        $contractUpgrade->setStudent($student);
        $flow = $this->get('tsk_contract.form.flow.contractUpgrade');
        $flow->bind($contractUpgrade);


        $form = $flow->createForm($contractUpgrade);
        if ($flow->isValid($form)) {
            $flow->saveCurrentStepData();

            if ($flow->nextStep()) {
                $form = $flow->createForm($contractUpgrade);
                if ($contractUpgrade->getProgram()) {
                    $credits = 'blah';
                } else {
                    $credits = 'foob';
                }
            } else {
                $flow->reset();
                return $this->redirect($this->generateUrl('tsk_student_default_registerstudent'));
            }
        }

        return array('form' => $form->createView(), 'flow' => $flow, 'contract' => $contract, 'today' => new \DateTime());
    }

    /**
     * @Route("/admin/contract/modify/{id}", name="tsk_contract_modify")
     * @Template()
     */
    public function modifyAction(Student $student)
    {
        $contract = $student->getActiveContract();
        if (!$contract) {
            throw new \Exception('No active contract for student');
        }
        return array('contract' => $contract);

    }

    /**
     * @Route("/admin/contract/cancel/{id}", name="tsk_contract_cancel")
     * @Template()
     */
    public function cancelAction(Contract $contract)
    {
        if (!$contract->getIsActive()) {
            throw new \Exception('Contract is not active');
        }

        $request = $this->getRequest();
        $form = $this->createFormBuilder()
            ->add('reason', 'choice', array(
                'choices' => array(
                                'moving' => 'Moving',
                                'injury' => 'Injury',
                                'other' => 'Other ...'
                ),
                'empty_value' => '-- select reason',
                'label' => 'Why are you cancelling this contract?',
                'attr' => array('class' => 'input-medium')
            ))
            ->add('reason_txt', 'text', array(
                'required' => false,
                'attr' => array('class' => 'reason_txt input-small')
            ))
            ->getForm();

        $form->bind($request);
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                // fire cancelEvent where all contract manipulation occurs ...
                $event = new \TSK\ContractBundle\Event\ContractEvent($contract);
                $dispatcher = $this->container->get('event_dispatcher');
                $dispatcher->dispatch(ContractEvents::CANCEL, $event);
                $this->get('session')->getFlashBag()->add('success', 'Contract cancelled');
                return $this->redirect($this->generateUrl('admin_tsk_student_student_list'));
            }
        }
        return array('contract' => $contract, 'form' => $form->createView());
    }

    /**
     * @Route("/admin/contract/freeze/{id}", name="tsk_contract_freeze")
     * @Template()
     */
    public function freezeAction(Contract $contract)
    {
        if (!$contract->getIsActive()) {
            throw new \Exception('Contract is not active');
        }
        $request = $this->getRequest();
        $contractFreeze = new \TSK\ContractBundle\Form\Model\ContractFreeze();
        $contractFreeze->setContract($contract);
        $contractFreeze->setStudent($contract->getStudents()->first());
        $form = $this->createForm(new \TSK\ContractBundle\Form\Type\ContractFreezeType(), $contractFreeze);

        if ($request->isMethod('POST')) {
            $form->bind($request);
            if ($form->isValid()) {
                // Validate contractFreeze
                // - sum of freeze days plus newly requested freeze days ust be less than MaxFreezeDays
                // - freeze days should not overlap with any existing freeze days
                // - freeze start_date >= today()
                // - start_date < end_date
                $em = $this->getDoctrine()->getManager();
                $cfRepo = $em->getRepository('TSK\ContractBundle\Entity\ContractFreeze');
                $existingFreezeDays = $cfRepo->sumByContract($contractFreeze->getContract());
                $requestedFreezeDays = $contractFreeze->getTotalFreezeDays();
                $limit = 60;
                if ($existingFreezeDays + $requestedFreezeDays < $limit) {
                    if ($cfRepo->datesOverlapForContract($contractFreeze->getContract(), $contractFreeze->getStartDate(), $contractFreeze->getEndDate())) {
                        $this->get('session')->getFlashBag()->add('error', 'Unable to honor freeze request.  Start date and end date already overlap');
                    } else {
                        // Save contract freeze
                        $cf = new \TSK\ContractBundle\Entity\ContractFreeze();
                        $cf->setStudent($contractFreeze->getStudent());
                        $cf->setContract($contractFreeze->getContract());
                        $cf->setStartDate($contractFreeze->getStartDate());
                        $cf->setEndDate($contractFreeze->getEndDate());
                        $em->persist($cf);
                        $em->flush();
                        $this->get('session')->getFlashBag()->add('success', 'Contract freeze saved!');
                    }
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'Unable to honor freeze request.  Request would put student over ' . $limit . ' day limit');
                }
                // return $this->redirect($this->generateUrl('admin_tsk_student_student_list'));
            }
        }
        return array('form' => $form->createView());
    }

    /**
     * @Route("/admin/contract/view/{id}", name="tsk_contract_default_view", defaults={"_format" = "html" })
     * @Template()
     */
    public function viewAction(Contract $contract)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $doc = $contract->getLatestContractDoc();
        $paymentTermsJSON = $doc->getPaymentTerms();
        // ld($paymentTermsJSON);
        $paymentTerms = json_decode($paymentTermsJSON['paymentsData']);
        // $paymentTerms = new \StdClass();
        // foreach ($paymentTermsJSON as $k => $v) {
        //     $paymentTerms->$k = $v;
        // }

        $s = new String();
        $installments = $s->stringifyPayments($paymentTerms->payments);

        // Get contract school and school state
        // $school = $doc->getSchool();
        $contractTemplate = $contract->getProgram()->getMembershipType()->getContractTemplate();

        if ($contractTemplate)  {
            $template = $contractTemplate->getTemplate();

            $orgId = $this->get('session')->get('tsk_organization_id');

            $orgRepo = $em->getRepository('TSK\UserBundle\Entity\Organization'); 
            $org = $orgRepo->find($orgId);

            $stringRenderer = $this->get('tsk.twig.string');
            $output = $stringRenderer->render($template, array(
                'contractAmount' => $paymentTerms->principal,
                'contractDiscount' => (!empty($paymentTerms->discount)) ? $paymentTerms->discount : '',
                'contractPayments' => $paymentTerms->payments,
                'contractNumPayments' => count($paymentTerms->payments),
                'schoolLegalName' => $doc->getSchoolLegalName(), 
                'schoolAddress1' => $doc->getSchoolAddress1(), 
                'schoolAddress2' => $doc->getSchoolAddress2(), 
                'schoolCity' => $doc->getSchoolCity(), 
                'schoolState' => $doc->getSchoolState(), 
                'schoolPostalCode' => $doc->getSchoolPostalCode(), 
                'schoolPhone' => $doc->getSchoolPhone(), 
                'schoolLateGraceDays' => $doc->getSchoolLateGraceDays(), 
                'schoolLatePaymentCharge' => $doc->getSchoolLatePaymentCharge(), 
                'programLegalDescription' => $doc->getProgramLegalDescription(),
                'org' => $org,
                'orgName' => $org->getTitle(),
                'abbrOrgName' => $org->getTitle(),
                'blackBeltFee' => 495,  // config var
                'student' => $doc->getStudents(), 
                'installments' => '$installments')
            );

            // $xml = $response->getContent();
            $response = new Response();
            $facade = $this->get('ps_pdf.facade');
            $content = $facade->render($output);
            return new Response($content, 200, array('content-type' => 'application/pdf'));
            exit;
        }

        
        
        $refundPolicy = $this->generateRefundPolicy($student->getContracts()->count(), $school->getContact()->getState()->getStateName(), $latestContract->getAmount());
        $cancelPolicy = $this->generateCancelPolicy($school->getContact()->getState()->getStateName());
        $format = $this->get('request')->get('_format');
        return $this->render('TSKContractBundle:Default:index.pdf.twig', 
            array(
                'contract' => $latestContract, 
                'school' => $school, 
                'orgName' => "Tiger Schulmann's Mixed Martial Arts Center", 
                'abbrOrgName' => 'TSMMA',
                'blackBeltFee' => 495,
                'student' => $student, 
                'refundPolicy' => $refundPolicy,
                'cancelPolicy' => $cancelPolicy,
                'installments' => $installments)
        );
    }

    /**
     * @Route("/admin/contracttemplate/activate/{id}/{version}", name="tsk_contract_default_activate", options={"expose"=true})
     * @Template()
     */
    public function activateAction(ContractTemplate $template, $version)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $ctvRepo = $em->getRepository('TSK\ContractBundle\Entity\ContractTemplateVersion'); 
        try {
            $ctv = $ctvRepo->findOneBy(array('objectId' => $template->getId(), 'version' => $version)); 
            $ctv->setActiveDate(); 
            $em->persist($ctv); 
            $em->flush();
            $success = true;
            $message = '';
        } catch (\Exception $e) {
            $success = false;
            $message = $e->getMessage();
        }

        $response = new JsonResponse();
        $response->setData(array('success' => $success, 'message' => $message));
        return $response;
    }

    /**
     * @Route("/admin/contract/summary/{id}", name="tsk_contract_default_summary")
     * @Template()
     */
    public function summaryAction(Contract $contract)
    {
        $em = $this->getDoctrine()->getManager();
        $bpmContract = $em->getRepository('TSK\BilleeBundle\Entity\BilleePaymentMethodContract');
        $bpmcs = $bpmContract->findBy(array('contract' => $contract));
        $school = $this->get('session')->get('tsk_school_id');

        $contractUpgrade = new ContractUpgrade();
        $contractUpgrade->setContract($contract);
        $form = $this->createForm(new UpgradeContractType(), $contractUpgrade, array('school' => $school));
        return array('form' => $form->createView(), 'contract' => $contract, 'bpmcs' => $bpmcs);
    }

    /**
     * @Route("/admin/contract/preview/{id}", name="tsk_contract_default_preview")
     * @Template()
     */
    public function upgradePreviewAction(Contract $contract)
    {
        $school = $this->get('session')->get('tsk_school_id');

        $contractUpgrade = new ContractUpgrade();
        $contractUpgrade->setContract($contract);
        $form = $this->createForm(new UpgradeContractType(), $contractUpgrade, array('school' => $school));
        $request = $this->getRequest();
        $form->bind($request);

        $request = $this->getRequest();
        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                print 'yes got preview';
            }
        }
        return array('form' => $form->createView(), 'contract' => $contract);
    }
}
