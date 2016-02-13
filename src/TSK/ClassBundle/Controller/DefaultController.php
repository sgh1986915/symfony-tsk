<?php

namespace TSK\ClassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use APY\DataGridBundle\Grid\Source\Entity;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/class/grid")
     * @Template()
     */
    public function indexAction()
    {
        // $source = new Entity('TSKStudentBundle:Student');
        $source = new Entity('TSKClassBundle:Classes');
        $grid = $this->get('grid');
        $grid->setSource($source);

        // Return the response of the grid to the template
        return $grid->getGridResponse('TSKClassBundle:Default:grid.html.twig');
        // return $this->render('TSKClassBundle:Default:grid.html.twig', array('grid' => $grid));
    }
}
