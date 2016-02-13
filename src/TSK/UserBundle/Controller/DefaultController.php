<?php

namespace TSK\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use TSK\UserBundle\Entity\Contact;
use TSK\UserBundle\Form\Type\ContactType;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/dashboard")
     * @Template()
     */
    public function indexAction()
    {
    	$name = "Dashboard";
		
        return array('name' => $name);
    }

    /**
     * @Route("/tskcontact")
     * @Template()
     */
    public function newAction(Request $request)
    {
/*
        $form = $this->createFormBuilder(new Contact())
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->add('email', 'text')
            ->getForm();
*/
        $form = $this->createForm(new ContactType(), new Contact());

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $c = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($c);
                $em->flush(); 

                return $this->redirect($this->generateUrl('tsk_user_default_index', array('name' => $c->getFirstName())));
            }
        }


        return $this->render('TSKUserBundle:Default:new.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
