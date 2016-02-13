<?php

namespace TSK\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use TSK\ScheduleBundle\Form\Type\RepeatOptionsType;

class DefaultController extends Controller
{
    /**
     * @Route("/admin/scheduler")
     * @Template()
     */
    public function indexAction()
    {
        $name = 'foo';
        return array('name' => $name);
    }

    /**
     * @Route("/admin/schedule/repeat_options")
     * @Template("TSKScheduleBundle:Default:repeat.html.twig")
     */
    public function repeatAction()
    {
        $form = $this->get('form.factory')->create(new RepeatOptionsType());
        return array('form' => $form->createView());
    }

    /**
     * @Route("/admin/schedule/test")
     * @Template("TSKScheduleBundle:Default:repeat.html.twig")
     */
    public function testAction()
    {
        $em = $this->getDoctrine()->getManager();
        $scheduleEntity = $em->getRepository('TSK\ScheduleBundle\Entity\ScheduleEntity')->find(8);
        // $processor = $this->get('tsk_schedule.processor.schedule_instances');
        // $processor->processEntity($scheduleEntity);
        $scheduleEntity->setTitle('yoga ' . time());
        $em->persist($scheduleEntity);
        $em->flush();
        ld($scheduleEntity); exit;
        return array('form' => 'foo');
    }
}
