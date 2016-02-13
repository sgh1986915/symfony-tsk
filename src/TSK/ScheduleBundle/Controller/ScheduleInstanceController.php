<?php

namespace TSK\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use TSK\ScheduleBundle\Entity\ScheduleInstance;
use TSK\ScheduleBundle\Form\Type\RosterFormType;
use TSK\ScheduleBundle\Entity\Roster;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Util\Codes;
use TSK\UserBundle\Form\Type\ContactType;
use TSK\UserBundle\Entity\Contact;

class ScheduleInstanceController extends Controller
{
    /**
     * drag
     * Process drag event on scheduleInstance (add to both start and end of event)
     * @Route("/admin/schedule/{id}/drag/{dayDelta}/{minuteDelta}/{isAllDay}", options={"expose"=true})
     * @Template("TSKScheduleBundle:Default:drag.html.twig")
     * @Method({"POST", "GET"})
     */
    public function dragAction(ScheduleInstance $scheduleInstance, $dayDelta, $minuteDelta, $isAllDay)
    {
        $interval = sprintf('P%dDT%dM', $dayDelta, $minuteDelta);
        $start = $scheduleInstance->getStart();
        $end = $scheduleInstance->getEnd();
        $finalStart = $this->addInterval($start, $dayDelta, 'day');
        $finalStart = $this->addInterval($finalStart, $minuteDelta, 'minute');
        $finalEnd = $this->addInterval($end, $dayDelta, 'day');
        $finalEnd = $this->addInterval($finalEnd, $minuteDelta, 'minute');
        
        $scheduleInstance->setStart($finalStart);
        $scheduleInstance->setEnd($finalEnd);
        $scheduleInstance->setIsAllDay($isAllDay == 1);
       
        $em = $this->getDoctrine()->getManager();
        $em->persist($scheduleInstance);
        $em->flush();
        return array('response' => '1');
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($scheduleInstance)
          ->setTemplate('TSKScheduleBundle:Default:drag.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * resize
     * Process resize event on scheduleInstance (add to end of event)
     * @Route("/admin/schedule/{id}/resize/{dayDelta}/{minuteDelta}", options={"expose"=true})
     * @Template("TSKScheduleBundle:Default:drag.html.twig")
     * @Method("POST")
     */
    public function resizeAction(ScheduleInstance $scheduleInstance, $dayDelta, $minuteDelta)
    {
        $end = $scheduleInstance->getEnd();
        $finalEnd = $this->addInterval($end, $dayDelta, 'day');
        $finalEnd = $this->addInterval($finalEnd, $minuteDelta, 'minute');
        // $finalEnd = $finalEnd->add(new \DateInterval($interval));
        $scheduleInstance->setEnd($finalEnd);
        $em = $this->getDoctrine()->getManager();
        $em->persist($scheduleInstance);
        $em->flush();
        return array('response' => '1');
        $view = View::create()  
          ->setStatusCode(200)  
          ->setData($scheduleInstance)
          ->setTemplate('TSKScheduleBundle:Default:drag.html.twig');
        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * detail
     * scheduleInstance detail 
     * @Route("/admin/schedule/{id}/detail", options={"expose"=true})
     * @Template("TSKScheduleBundle:Default:detail.html.twig")
     * @Method("GET")
     */
    public function detailAction(ScheduleInstance $scheduleInstance)
    {
        $payload = array('scheduleInstance' => $scheduleInstance);
        if ($scheduleInstance->getScheduleEntity()->getCategory()->getName() == 'class') {
            $classes = $scheduleInstance->getScheduleEntity()->getClasses();
        }
        return $payload;
    }

    /**
     * addInterval 
     * Adds a set amount of time to date object
     * 
     * @param DateTime $origDateTime  // DateTime obj to be acted upon
     * @param integer $amount  // amount to add (can be pos or neg)
     * @param string $unit  // "day" or "minute"
     * @access public
     * @return DateTime
     */
    public function addInterval(\DateTime $origDateTime, $amount, $unit)
    {
        $finalDateTime = clone $origDateTime;
        if ($amount != 0) {
            $method = ($amount > 0) ? 'add' : 'sub';
            $amount = abs($amount);
            switch ($unit) {
                case 'day':
                    $interval = sprintf('P%dD', $amount);
                break;

                case 'minute':
                    $interval = sprintf('PT%dM', $amount);
                break;

                default:
                    throw new \Exception("Invalid unit $unit");
                break;
            }
            $finalDateTime->{$method}(new \DateInterval($interval));
        }
        return $finalDateTime;
    }
}
