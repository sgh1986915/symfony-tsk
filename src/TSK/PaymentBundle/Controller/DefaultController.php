<?php

namespace TSK\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TSK\UserBundle\Entity\Contact;
use TSK\PaymentBundle\Entity\Payment;
use TSK\PaymentBundle\Entity\ChargePayment;
use TSK\ProgramBundle\Entity\ProgramPaymentPlan;
use TSK\PaymentBundle\Form\Model\ReceivePayment;
use TSK\PaymentBundle\Form\Type\ReceivePaymentType;
use Doctrine\ORM\EntityRepository;
use TSK\PaymentBundle\Event\PaymentEvent;
use TSK\PaymentBundle\Event\PaymentEvents;
use VinceG\FirstDataApi\FirstData;
use TSK\ContractBundle\Entity\Contract;
use TSK\PaymentBundle\Util\Deferral;


class DefaultController extends Controller
{
    /**
     * @Route("/receivePayment/{contactID}", defaults={"contactID" = 0})
     * @Method({"GET","POST"})
     * @Template()
     */
    public function indexAction($contactID=0)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = new ReceivePayment();
        $paymentType = $em->getRepository('TSKPaymentBundle:PaymentType')->findOneBy(array('name' => 'payment'));
        $payment->setPaymentType($paymentType);
        $payment->setPaymentDistributionStrategy('eagerly');
        if ($contactID) {
            $contact = $em->getRepository('TSKUserBundle:Contact')->find($contactID);
            $payment->setContact($contact);
            if (!$contact) {
                // throw new HttpException(404, "contact not found");
                $this->get('session')->getFlashBag()->add('error', 'contact not found');
                return $this->redirect($this->generateUrl('tsk_payment_default_index'));
            }
        }

        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment, array('show_date' => $this->canModifyDate()));
        
        return array('entity' => $payment,
                     'form' => $form->createView()
        );
    }

    /**
     * @Route("/applyCredit/{contactID}", defaults={"contactID" = 0})
     * @Method({"GET","POST"})
     * @Template()
     */
    public function creditAction($contactID=0)
    {
        $em = $this->getDoctrine()->getManager();
        $payment = new ReceivePayment();
        $paymentType = $em->getRepository('TSKPaymentBundle:PaymentType')->findOneBy(array('name' => 'credit'));
        $payment->setPaymentType($paymentType);
        $creditPaymentMethod = $em->getRepository('TSKPaymentBundle:PaymentMethod')->findOneBy(array('name' => 'CREDIT'));
        $payment->setPaymentMethod($creditPaymentMethod);
        $payment->setPaymentDistributionStrategy('evenly');
        if ($contactID) {
            $contact = $em->getRepository('TSKUserBundle:Contact')->find($contactID);
            $payment->setContact($contact);
            if (!$contact) {
                // throw new HttpException(404, "contact not found");
                $this->get('session')->getFlashBag()->add('error', 'contact not found');
                return $this->redirect($this->generateUrl('tsk_payment_default_index'));
            }
        }

        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment, array('is_credit' => true, 'show_date' => $this->canModifyDate()));
        
        return array('entity' => $payment,
                     'form' => $form->createView()
        );
    }

    private function canModifyDate()
    {
        $sc = $this->get('security.context');
        $showDate = false;
        foreach ($sc->getToken()->getRoles() as $role) {
            if (($role->getRole() == 'ROLE_ORG_ADMIN') || ($role->getRole() == 'ROLE_SUPER_ADMIN')) {
                $showDate = true;
            }
        }
        return $showDate;
    }

    /**
     * Saves a payment
     *
     * @Route("/", name="payment_save")
     * @Method("POST")
     * @Template("TSKPaymentBundle:Default:index.html.twig")
     */
    public function saveAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $sessionKey = $this->container->getParameter('tsk_user.session.org_key');
        $schoolKey = $this->container->getParameter('tsk_user.session.school_key');
        $schoolId = $session->get($schoolKey);
    
        $em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolId);
        // this is a little hokey ... searching for payment type when we could otherwise embed it.
        $paymentType = $em->getRepository('TSKPaymentBundle:PaymentType')->findOneBy(array('organization' => $session->get($sessionKey), 'name' => 'payment'));
        $payment = new ReceivePayment();
        if ($paymentType) {
            $payment->setPaymentType($paymentType);
        } else {
            throw new HttpException(404, "Invalid payment type");
        }

        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment, array('show_date' => $this->canModifyDate()));
        $form->bind($request);

        if ($form->isValid()) {
            $authorizationNum = '';
            if ($payment->getPaymentMethod()->getPaymentType() == 'CREDIT CARD') {
                // if credit card run payment through E4
                $paymentGateway = $this->container->get('tsk_payment.gateway.service');
                $paymentGateway->setCardHoldersName((string) $payment->getContact());
                $paymentGateway->setCreditCardNumber($payment->getCreditCardNumber());
                $paymentGateway->setCreditCardExpiration($payment->getCreditCardExpirationDate());
                $paymentGateway->setCreditCardVerification($payment->getCvvNumber());
                $paymentGateway->setCreditCardType($payment->getPaymentMethod()->getName());
                try {
                    $result = $paymentGateway->purchase($payment->getPaymentAmount());
                    $session->getFlashBag()->add('error', 'There was an error saving your payment!');
                    return $this->redirect($this->generateUrl('tsk_student_default_registerstudent'));
                } catch (\Exception $e) {
                }
                $obj = json_decode($result);
                $authorizationNum = $obj->authorization_num;
            }

            $pmt = new Payment();
            $pmt->setSchool($school);
            $pmt->setPaymentAmount($payment->getPaymentAmount());
            $pmt->setPaymentMethod($payment->getPaymentMethod());
            $pmt->setPaymentType($payment->getPaymentType());
            $pmt->setDescription($payment->getMemo());
            $pmt->setRefNumber($payment->getRefNumber());
            $paymentDate = ($payment->getPaymentDate()) ? $payment->getPaymentDate() : new \DateTime();
            // $pmt->setCreatedDate($paymentDate);
            $pmt->setAuthorizationNum($authorizationNum);
            $em->persist($pmt);

            $payments = $this->get('request')->request->get('payments');
            foreach ($payment->getCharges() as $charge) {
                if (!empty($payments[$charge->getId()])) {
                    $cp = new ChargePayment();
                    $cp->setCharge($charge);
                    $cp->setPayment($pmt);
                    $cp->setAmount($payments[$charge->getId()]);
                    $em->persist($cp);
                }
            }
            // write to journal?
            $em->flush();
            
            $dispatcher = $this->get('event_dispatcher');
            $paymentEvent = new PaymentEvent($pmt);
            try {
                $dispatcher->dispatch(PaymentEvents::RECEIVE, $paymentEvent);
            } catch (\Exception $e) {
                throw $e;
            }
            $session->getFlashBag()->add('success', 'Payment saved and applied!');

            return $this->redirect($this->generateUrl('tsk_payment_default_index', array('contactID' => $payment->getContact()->getId())));
        } else {
            $session->getFlashBag()->add('error', 'There was an error saving your payment!');
        }

        return array('entity' => $payment,
                     'form' => $form->createView()
        );
    }

    /**
     * Saves a payment
     *
     * @Route("/", name="credit_save")
     * @Method("POST")
     * @Template("TSKPaymentBundle:Default:credit.html.twig")
     */
    public function creditSaveAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        $sessionKey = $this->container->getParameter('tsk_user.session.org_key');
        $schoolKey = $this->container->getParameter('tsk_user.session.school_key');
        $schoolId = $session->get($schoolKey);
    
        $em = $this->getDoctrine()->getManager();
        $school = $em->getRepository('TSKSchoolBundle:School')->find($schoolId);
        // this is a little hokey ... searching for payment type when we could otherwise embed it.
        $paymentType = $em->getRepository('TSKPaymentBundle:PaymentType')->findOneBy(array('organization' => $session->get($sessionKey), 'name' => 'credit'));
        $payment = new ReceivePayment();
        if ($paymentType) {
            $payment->setPaymentType($paymentType);
        } else {
            throw new HttpException(404, "Invalid payment type");
        }

        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment, array('is_credit' => true, 'show_date' => $this->canModifyDate()));
        $form->bind($request);

        if ($form->isValid()) {
            $authorizationNum = '';
            if ($payment->getPaymentMethod()->getPaymentType() == 'CREDIT CARD') {
                // if credit card run payment through E4
                $paymentGateway = $this->container->get('tsk_payment.gateway.service');
                $paymentGateway->setCardHoldersName((string) $payment->getContact());
                $paymentGateway->setCreditCardNumber($payment->getCreditCardNumber());
                $paymentGateway->setCreditCardExpiration($payment->getCreditCardExpirationDate());
                $paymentGateway->setCreditCardVerification($payment->getCvvNumber());
                $paymentGateway->setCreditCardType($payment->getPaymentMethod()->getName());
                try {
                    $result = $paymentGateway->purchase($payment->getPaymentAmount());
                    $session->getFlashBag()->add('error', 'There was an error saving your payment!');
                    return $this->redirect($this->generateUrl('tsk_student_default_registerstudent'));
                } catch (\Exception $e) {
                }
                $obj = json_decode($result);
                $authorizationNum = $obj->authorization_num;
            }

            $pmt = new Payment();
            $pmt->setSchool($school);
            $pmt->setPaymentAmount($payment->getPaymentAmount());
            $pmt->setPaymentMethod($payment->getPaymentMethod());
            $pmt->setPaymentType($payment->getPaymentType());
            $pmt->setDescription($payment->getMemo());
            $pmt->setRefNumber($payment->getRefNumber());
            $paymentDate = ($payment->getPaymentDate()) ? $payment->getPaymentDate() : new \DateTime();
            // $pmt->setCreatedDate($paymentDate);
            $pmt->setAuthorizationNum($authorizationNum);
            $em->persist($pmt);

            $payments = $this->get('request')->request->get('payments');
            foreach ($payment->getCharges() as $charge) {
                if (!empty($payments[$charge->getId()])) {
                    $cp = new ChargePayment();
                    $cp->setCharge($charge);
                    $cp->setPayment($pmt);
                    $cp->setAmount($payments[$charge->getId()]);
                    $em->persist($cp);
                }
            }
            // write to journal?
            $em->flush();
            
            $dispatcher = $this->get('event_dispatcher');
            $paymentEvent = new PaymentEvent($pmt);
            try {
                $dispatcher->dispatch(PaymentEvents::RECEIVE, $paymentEvent);
            } catch (\Exception $e) {
                throw $e;
            }
            $session->getFlashBag()->add('success', 'Payment saved and applied!');

            return $this->redirect($this->generateUrl('tsk_payment_default_index', array('contactID' => $payment->getContact()->getId())));
        } else {
            $session->getFlashBag()->add('error', 'There was an error saving your payment!');
        }

        return array('entity' => $payment,
                     'form' => $form->createView()
        );
    }

    /**
     * @Route("/getplan/{program_payment_plan_id}", defaults={"_format"="json"}, options={"expose"=true})
     * @ParamConverter("plan", class="TSKProgramBundle:ProgramPaymentPlan", options={"id"="program_payment_plan_id"})
     * @Template()
     */
    public function planAction(ProgramPaymentPlan $plan)
    {
        return array('plan' => $plan);
    }

    /**
     * @Route("/calc/{programPaymentPlanID}", defaults={"programPaymentPlanID" = 0})
     * @Template()
     */
    public function calcAction($programPaymentPlanID)
    {
        $maxDiscount = 100;
        $isAdmin = false;
        $sc = $this->get('security.context');
        foreach ($sc->getToken()->getRoles() as $role) {
            if (($role->getRole() == 'ROLE_ORG_ADMIN') || ($role->getRole() == 'ROLE_SUPER_ADMIN')) {
                $maxDiscount = 0;
                $isAdmin = true;
            }
        }

        if ($programPaymentPlanID) {
            $em = $this->getDoctrine()->getManager();
            $pp = $em->getRepository('TSKProgramBundle:ProgramPaymentPlan')->find($programPaymentPlanID);
            if (!$pp) {
                throw new HttpException(404, "entity not found");
            }
            $principal = $pp->getPaymentsDataValue('principal');
            $num_payments =  count($pp->getPaymentsDataValue('payments'));
            $payment_term = $pp->getPaymentsDataValue('payment_term');
            $prePayment = $pp->getPaymentsDataValue('prePayment');
            $paymentsData = $pp->getPaymentsData();
            $paymentsData = $paymentsData['paymentsData'];
        }
        if (!isset($paymentsData)) {
            $paymentsData = '';
        }

        if (!isset($principal)) {
            $principal = 0;
        }
 
        if (!isset($prePayment)) {
            $prePayment = 0;
        }

        if (!isset($credit)) {
            $credit = 0;
        }

        if (!isset($discount)) {
            $discount = 0;
        }

        if (!isset($num_payments)) {
            $num_payments = 0;
        }

        $request = $this->getRequest();
        $holder = ($request->query->get('rnd')) ?: mt_rand();
        
        return array(
                'paymentsData' => $paymentsData,
                'principal' => (float)$principal, 
                'num_payments' => $num_payments, 
                'holder' => $holder,
                'discount' => $discount,
                'credit' => $credit,
                'prePayment' => $prePayment,
                'maxDiscount' => $maxDiscount,
                'isAdmin' => $isAdmin
        );
    }

    /**
     * @Route("/customize/{programPaymentPlanID}", defaults={"programPaymentPlanID" = 0})
     * @Template()
     */
    public function customizeAction($programPaymentPlanID)
    {
        if ($programPaymentPlanID) {
            $em = $this->getDoctrine()->getManager();
            $pp = $em->getRepository('TSKProgramBundle:ProgramPaymentPlan')->find($programPaymentPlanID);
            if (!$pp) {
                throw new HttpException(404, "entity not found");
            }
            $principal = $pp->getPaymentsDataValue('principal');
            $num_payments =  count($pp->getPaymentsDataValue('payments'));
            $payment_term = $pp->getPaymentsDataValue('payment_term');
            $discount = $pp->getPaymentsDataValue('discount');
            $paymentsData = $pp->getPaymentsData();
            $paymentsData = $paymentsData['paymentsData'];
        }
        if (!isset($paymentsData)) {
            $paymentsData = '';
        }
 
        if (!isset($principal)) {
            $principal = 1000;
        }

        if (!isset($num_payments)) {
            $num_payments = 5;
        }

        if (!isset($discount)) {
            $discount = 0;
        }

        if (!isset($prePayment)) {
            $prePayment = 0;
        }

        $request = $this->getRequest();
        $holder = ($request->query->get('rnd')) ?: mt_rand();
        
        return array(
                'paymentsData' => $paymentsData,
                'principal' => (float)$principal, 
                'num_payments' => $num_payments, 
                'holder' => $holder,
                'discount' => $discount,
                'prePayment' => $prePayment
        );
    }

    /**
     * @Route("/cust/")
     * @Template("TSKPaymentBundle:Default:calc.html.twig")
     */
    public function custAction()
    {
        $sc = $this->get('security.context');
        $maxDiscount = 100;
        $isAdmin = false;
        foreach ($sc->getToken()->getRoles() as $role) {
            if (($role->getRole() == 'ROLE_ORG_ADMIN') || ($role->getRole() == 'ROLE_SUPER_ADMIN')) {
                $maxDiscount = 0;
                $isAdmin = true;
            }
        }

        $request = $this->getRequest();
    
        $paymentsData = $this->getRequest()->request->get('paymentsData');
        
        $holder = ($request->query->get('rnd')) ?: mt_rand();
        
        return array(
                'paymentsData' => json_encode($paymentsData),
                'principal' => (float)$paymentsData['principal'], 
                'num_payments' => count($paymentsData['payments']),
                'holder' => $holder,
                'credit' => (!empty($paymentsData['credit'])) ? $paymentsData['credit'] : 0,
                'discount' => (!empty($paymentsData['discount'])) ? $paymentsData['discount'] : 0,
                'prePayment' => (!empty($paymentsData['prePayment'])) ? $paymentsData['prePayment'] : 0,
                'maxDiscount' => $maxDiscount,
                'isAdmin' => $isAdmin
        );
    }

    /**
     * getChargesAction 
     * Gets open charges for a given contact
     * 
     * @Route("/get_open_charges/{contactID}")
     * @Template()
     * @access public
     * @ParamConverter("contact", class="TSKUserBundle:Contact",  options={"id" = "contactID"})
     * 
     */
    public function fgetOpenChargesAction(Contact $contact)
    {
        $em = $this->getDoctrine()->getManager();
        $charges = $em->getRepository('TSKUserBundle:Contact')->getOpenChargesForContact($contact);
        return array('charges' => $charges);
    }

    /**
     * getOpenChargesAction
     * Gets open charges for a given contact
     * 
     * @Route("/get_open_charges", defaults={"_format"="html"}, options={"expose"=true})
     * @Template()
     * @Method("POST")
     * @access public
     * 
     */
    public function getOpenChargesAction(Request $request)
    {
        $payment = new ReceivePayment();
        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment);
        $form->bind($request);

        return array('form' => $form->createView());
    }

    /**
     * getPaymentMethodUIAction
     * Gets open charges for a given contact
     * 
     * @Route("/get_payment_method_ui", defaults={"_format"="html"}, options={"expose"=true})
     * @Template()
     * @Method("POST")
     * @access public
     * 
     */
    public function getPaymentMethodUIAction(Request $request)
    {
        $payment = new ReceivePayment();
        $form = $this->createForm($this->get('tsk_payment.form.type.receivePayment'), $payment);
        $form->bind($request);

        return array('entity' => $payment,
                     'form' => $form->createView()
        );
    }

    /**
     * @Route("/dropdown/{discountTypeId}", options={"expose"=true})
     * @Template()
     * @Method({"GET"})
     */
    public function dropdownAction($discountTypeId=null)
    {
        $org = $this->getRequest()->getSession()->get('tsk_organization_id');
        $em = $this->getDoctrine()->getManager();
        if ($discountTypeId) {
            $data = $em->getRepository('TSKPaymentBundle:DiscountType')->find($discountTypeId);
        } else {
            $data = null;
        }
        $form = $this->createFormBuilder()
            ->add('value', 'entity', array(
                        'class' => 'TSKPaymentBundle:DiscountType',
                        'data' => $data,
                        'expanded' => false,
                        'multiple' => false,
                        'empty_value' => '-- Select discount type',
                        'query_builder' => function(EntityRepository $er) use ($org) {
                            return $er->createQueryBuilder('u')
                                ->where('u.organization=:org')
                                ->setParameter(':org', $org);
                        }
                        ))
            ->getForm();

        return $this->render('TSKPaymentBundle:Default:drawDiscountTypeForm.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/defer/{id}/{amount}/{contractStartDate}")
     * @Template()
     * @Method({"GET"})
     */
    public function testAction(Payment $payment, $amount, \DateTime $contractStartDate=null)
    {
        $em = $this->getDoctrine()->getManager();

        $cpRepo = $em->getRepository('TSK\PaymentBundle\Entity\ChargePayment');
        $chargePayments = $cpRepo->findBy(array('payment' => $payment));
        foreach ($chargePayments as $cp) {
            if ($cp->getCharge()->getAccount()->getName() == 'Inc Fm Students') {
                $contracts = $cp->getCharge()->getContracts();

                foreach ($contracts as $contract) {
                    $inits = $this->getMonthlyPrepayments($contract, $contractStartDate);
                    print '<pre>inits'; print_r($inits) . '</pre>';

                    $today = new \DateTime();
                    $terms = $contract->getPaymentTerms();
                    $obj = json_decode($terms['paymentsData']);
                    $d = new Deferral(
                        $obj->principal, 
                        $contract->getDeferralRate(), 
                        $contract->getDeferralDurationMonths(),
                        $inits,
                        $contractStartDate
                        // $contract->getContractStartDate()
                    );

                    $numFirsts = $this->countFirstOfMonthsSince($contractStartDate);
                    // $numFirsts = $this->countFirstOfMonthsSince($contract->getContractStartDate);

                    // $deferrals = $d->distributePaymentMax($cp->getAmount(), 9);
                    $deferrals = $d->distributePaymentMax($amount, $contract->getDeferralDurationMonths() - $numFirsts);
                    $Deferrals = $d->datestampPayments($deferrals);

                    $debitAccountName = $payment->getPaymentMethod()->getAccount()->getName();
                    $chargeDeferralAccountName = $cp->getCharge()->getDeferralAccount()->getName();
                    $chargeAccountName = $cp->getCharge()->getAccount()->getName();
                    foreach ($Deferrals as $DeferralDate => $DeferralAmount) {
                        print "$DeferralDate - $DeferralAmount<br>";
                        $DD = new \DateTime($DeferralDate);
                        if ($DeferralAmount) {
                            if ($DD <= $today) {
                                $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeAccountName, 'debitAccount' => $debitAccountName, 'debitAmount' => $DeferralAmount, 'creditAmount' => $DeferralAmount);
                                if ($amount > $DeferralAmount) {
                                    $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeDeferralAccountName, 'debitAccount' => $debitAccountName, 'debitAmount' => $amount - $DeferralAmount, 'creditAmount' => $amount - $DeferralAmount);
                                }
                            } else {
                                $results[] = array('date' => $DeferralDate, 'creditAccount' => $chargeAccountName, 'debitAccount' => $chargeDeferralAccountName, 'debitAmount' => $DeferralAmount, 'creditAmount' => $DeferralAmount);
                            }
                        }
                    }

                    function sorter($key)
                    {
                        return function($a, $b) use ($key) {
                                $ad = new \DateTime($a[$key]);
                                $bd = new \DateTime($b[$key]);
                                return $ad > $bd;
                        };
                    }
                    usort($results, sorter('date'));
                }
            }
        }
        return array('today' => $today, 'results' => $results, 'foo' => '$1200 contract, 10 payments @ $120, starting on '.$contractStartDate->format('Y-m-d').', deferral rate of 0.75, deferred over 10 months');
        // return array('foo' => '$1200 contract, 10 payments @ $120, starting on '.$contract->getContractStartDate()->format('Y-m-d').', deferral rate of 0.75, deferred over 10 months');
    }

    /**
     * getMonthlyPrepayments 
     * Takes a contract and determines how many payments have already
     * been made against charges assigned to this contract.  Payments
     * are grouped by calendar month.
     * 
     * @param Contract $contract 
     * @access public
     * @return void
     */
    public function getMonthlyPrepayments(Contract $contract, $contractStartDate=null)
    {
        // Initialize an array of $contract->getDeferralDurationMonths() buckets, indexed by month
        for ($j=0; $j < $contract->getDeferralDurationMonths(); $j++) {
            $bucketDate = clone $contractStartDate;
            // $bucketDate = clone $contract->getContractStartDate();
            $bucketDate->add(new \DateInterval('P'.$j.'M'));
            $buckets[$bucketDate->format('Y-m')] = 0;
        }

        // Query journal table to determine how much has already been realized by this contract?
        $conn = $this->getDoctrine()->getConnection();
        $stmt = $conn->prepare('select date_format(j.journal_date, "%m") as dmonth, year(j.journal_date) as dyear, sum(j.credit_amount) as damount from tsk_journal j inner join tsk_contract_charge cc where j.fk_credit_account_id=:credit_account_id AND cc.fk_contract_id=:contract_id and j.fk_charge_id=cc.fk_charge_id group by date_format(j.journal_date, "%m"), year(j.journal_date)');
        $stmt->bindValue(':credit_account_id', 4);
        $stmt->bindValue(':contract_id', $contract->getId());
        $stmt->execute();
        $prepayments = $stmt->fetchAll();

        // Add prepayments to buckets
        foreach ($prepayments as $pp) {
            $buckets[$pp['dyear'] .'-'.$pp['dmonth']] += $pp['damount'];
        }

        // Index bucket array by ints instead of months
        foreach ($buckets as $month => $bucketAmount) {
            $inits[] = $bucketAmount;
        }
        return $inits;
    }

    public function countFirstOfMonthsSince(\DateTime $date)
    {
        $today = new \DateTime();
        if ($today < $date) {
            return 0;
        }

        $delta = $date->diff($today);
        if ($delta->format('%R%a') < 800) {
            $firsts = 0;
            for ($i=1; $i < $delta->format('%a'); $i++) {
                $myDate = clone $date;
                $myDate->add(new \DateInterval('P'.$i.'D'));
                if ($myDate->format('d') == '1') {
                    $firsts++;
                }
            }
            return $firsts;
        } else {
            throw new \Exception('Date spread is too large ' . $delta->format('%R%a'));
        }
    }
}
