<?php
namespace TSK\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\View\View;
use TSK\UserBundle\Entity\Contact;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ContactController extends Controller
{
    public function getContactByNameAction()
    {   
        $request = $this->getRequest();
        $query = $request->query->get('query');
        $contacts = $this->getDoctrine()->getManager()->createQuery('SELECT c.firstName, c.lastName from TSKUserBundle:Contact c WHERE c.firstName LIKE :query OR c.lastName LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getResult();
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($contacts);
        return $this->get('fos_rest.view_handler')->handle($view);
    } // "get_contacts"     [GET] /contacts

    public function getContactById()
    {
        $request = $this->getRequest();
        $id = $request->query->get('id');
        $contact = $this->getDoctrine()->getManager()->createQuery('SELECT c.firstName, c.lastName from TSKUserBundle:Contact c WHERE c.id = :id')
            ->setParameter(':id', $id)
            ->getResult();
        $view = View::create()  
            ->setStatusCode(200)  
            -setData($contact);
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * get
     * Gets contact, restricted to contacts within your org/school
     * 
     * @ParamConverter("contact", class="TSKUserBundle:Contact")
     * @Template("TSKUserBundle:Default:get.html.twig")
     * @Method("GET")
     */
    public function getContactAction(Contact $contact)
    {
        $session = $this->getRequest()->getSession();
        $sessionKey = $this->container->getParameter('tsk_user.session.org_key');

        if ($contact->getOrganization()->getId() != $session->get($sessionKey)) {
            throw new HttpException(403, "Access forbidden");
        }
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($contact);
          // ->setTemplate('TSKUserBundle:Contact:get.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);

        return array('contact' => $contact);
    }


}
