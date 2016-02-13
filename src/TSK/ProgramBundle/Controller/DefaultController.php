<?php

namespace TSK\ProgramBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TSK\ProgramBundle\Entity\Program;
use Doctrine\ORM\EntityRepository;

class DefaultController extends Controller
{
    /**
     * @Route("/jello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TSKProgramBundle:Program')->find(12);
        print count($entity->getPaymentPlans());
        foreach ($entity->getPaymentPlans() as $pp) {
            if ($pp->getId() == 9) {
                $badPP = $pp;
            }
            print "<br>--   ". $pp->getName() . '(' . $pp->getId() . ')';
        }
        // print '<br>'; print get_class($entity);


        $entity->removePaymentPlan($badPP);
        $em->persist($entity);
        $em->flush();

        print '<h2>removed</h2>';
        return array('name' => $name);
    }

    /**
     * @Route("/mello/")
     * @Template("TSKProgramBundle:Default:index.html.twig")
     */
    public function fooAction()
    {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('TSKProgramBundle:Program')->find(12);
        $em->persist($entity);
        $em->flush();
        $name = 'done';
        return array('name' => $name);
    }
    /**
     * @Route("/ppdropdown/{id}", options={"expose"=true})
     * @Template()
     * @Method({"GET"})
     */
    public function programPaymentPlanAction(Program $program)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('programPaymentPlan', 'entity', array(
                        'class' => 'TSKProgramBundle:ProgramPaymentPlan',
                        'label' => 'selectLabel',
                        'property' => 'selectLabel',
                        'expanded' => false,
                        'multiple' => false,
                        'empty_value' => '-- Select payment plan',
                        'query_builder' => function(EntityRepository $er) use ($program) {
                            return $er->createQueryBuilder('u')
                                ->where('u.program=:program')
                                ->setParameter(':program', $program);
                        }))
            ->add('paymentPlanCustomizedPayments', 'tsk_payment_calculator', array(
                'label' => ' ', 
                'hide_data' => true, 
                'hide_button' => false))
            ->getForm();

        return $this->render('TSKProgramBundle:Default:drawProgramForm.html.twig', array('form' => $form->createView()));
    }
    
    /**
     * @Route("/dropdown/{programId}", options={"expose"=true})
     * @Template()
     * @Method({"GET"})
     */
    public function dropdownAction($programId=null)
    {
        $org = $this->getRequest()->getSession()->get('tsk_organization_id');
        $school = $this->getRequest()->getSession()->get('tsk_school_id');
        $em = $this->getDoctrine()->getManager();
        if ($programId) {
            $data = $em->getRepository('TSKProgramBundle:Program')->find($programId);
        } else {
            $data = null;
        }
        $form = $this->createFormBuilder()
            ->add('value', 'entity', array(
                        'class' => 'TSKProgramBundle:Program',
                        'data' => $data,
                        'expanded' => false,
                        'property' => 'selectLabel',
                        'multiple' => false,
                        'empty_value' => '-- Select program',
                        'query_builder' => function(EntityRepository $er) use ($org, $school) {
                            return $er->createQueryBuilder('u')
                                ->where('u.organization=:org')
                                ->andWhere(':school MEMBER OF u.schools')
                                ->andWhere('u.isActive=1')
                                ->andWhere('u.expirationDate > :today OR u.expirationDate IS NULL')
                                ->setParameter(':org', $org)
                                ->setParameter(':school', $school)
                                ->setParameter(':today', time());
    }))
            ->getForm();

        return $this->render('TSKProgramBundle:Default:drawProgramForm.html.twig', array('form' => $form->createView()));
    }

    /**
     * @Route("/membershipdropdown/{membershipTypeId}", options={"expose"=true})
     * @Template()
     * @Method({"GET"})
     */
    public function membershipDropdownAction($membershipTypeId=null)
    {
        $org = $this->getRequest()->getSession()->get('tsk_organization_id');
        $em = $this->getDoctrine()->getManager();
        if ($membershipTypeId) {
            $data = $em->getRepository('TSKProgramBundle:MembershipType')->find($membershipTypeId);
        } else {
            $data = null;
        }
        $form = $this->createFormBuilder()
            ->add('value', 'entity', array(
                        'class' => 'TSKProgramBundle:MembershipType',
                        'data' => $data,
                        'expanded' => false,
                        'multiple' => false,
                        'empty_value' => '-- Select membership type',
                        'query_builder' => function(EntityRepository $er) use ($org) {
                            return $er->createQueryBuilder('u')
                                ->where('u.organization=:org')
                                ->setParameter(':org', $org);
                        }
                        ))
            ->getForm();

        return $this->render('TSKProgramBundle:Default:drawProgramForm.html.twig', array('form' => $form->createView()));
    }
}
