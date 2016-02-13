<?php

namespace TSK\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use TSK\UserBundle\TSKUserCollection;
use TSK\UserBundle\Entity\User;
use TSK\UserBundle\Form\Type\UserType;
use FOS\RestBundle\View\View;

class UserController extends Controller
{
    /**
     * @Route("/tskuser")
     * @Template()
    public function newAction(Request $request)
    {
        $form = $this->createForm(new UserType(), new User());

        if ($request->isMethod('POST')) {
            $form->bind($request);

            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $user = $form->getData();
                $contact = $user->getContact();
                $em = $this->getDoctrine()->getManager();
                $user->setContact($contact);
                $em->persist($contact);
                $em->persist($user);
                $em->flush(); 

                return $this->redirect($this->generateUrl('tsk_user_default_index', array('name' => $user->getUsername())));
            }
        }


        return $this->render('TSKUserBundle:User:new.html.twig', array(
            'form' => $form->createView()
        ));
    }
     */

    public function getUsersAction()
    {   
        $users = $this->getDoctrine()->getRepository('TSKUserBundle:User')->findAll();
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($users);
        return $this->get('fos_rest.view_handler')->handle($view);
    } // "get_users"     [GET] /users

    /**
     * get
     * Gets contact, restricted to contacts within your org/school
     * 
     * @ParamConverter("user", class="TSKUserBundle:User")
     * @Template("TSKUserBundle:Default:get.html.twig")
     * @Method("GET")
     */
    public function getUserAction(User $user)
    {
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($user);
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    public function getUserByNameAction($query)
    {   
        $users = $this->getDoctrine()->getManager()->createQuery('SELECT u from TSKUserBundle:User u LEFT JOIN u.contact c WHERE c.firstName LIKE :query OR c.lastName LIKE :query OR u.username LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getResult();

        $tskusers = new TSKUserCollection($users);
        
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($tskusers);
        return $this->get('fos_rest.view_handler')->handle($view);
    } // "get_users"     [GET] /users

}
