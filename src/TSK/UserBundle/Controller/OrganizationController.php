<?php
namespace TSK\UserBundle\Controller;
use TSK\UserBundle\Entity\Organization;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OrganizationController extends Controller
{
    /**
     * @Route("/tskorg")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('TSKUserBundle:Organization')->findAll();
        return $this->render('TSKUserBundle:Organization:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    /**
     * @Route("/admin/org/switch/{id}", name="organization_switch", requirements={"id" = "\d+"})
     * @ParamConverter("organization", class="TSKUserBundle:Organization")
     * @Template()
     */
    public function switchAction(Organization $org)
    {
        $sessionKey = $this->container->getParameter('tsk_user.session.org_key');
        $this->get('session')->set($sessionKey, $org->getId());
        $this->get('session')->setFlash('notice', 'Your org has been switched');
        return $this->redirect($this->generateUrl('sonata_admin_dashboard'));
    }
}
