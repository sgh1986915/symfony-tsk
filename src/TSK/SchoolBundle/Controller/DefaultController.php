<?php

namespace TSK\SchoolBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use TSK\SchoolBundle\Entity\School;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }

    /**
     * @Route("/admin/school/switch/{id}", name="school_switch", requirements={"id" = "\d+"})
     * #ParamConverter("school", class="TSKSchoolBundle:School")#
     * @Method({"POST","GET"})
     * @Template()
     */
    public function switchAction(School $school)
    {
        $sessionKey = $this->container->getParameter('tsk_user.session.school_key');
        $this->get('session')->set($sessionKey, $school->getId());
        $this->get('session')->setFlash('notice', 'Your school has been switched');
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}
