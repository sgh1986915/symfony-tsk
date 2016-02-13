<?php

namespace TSK\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use TSK\ScheduleBundle\Entity\ScheduleInstance;
use TSK\ScheduleBundle\Entity\ScheduleAttendance;
use TSK\ScheduleBundle\Entity\Roster;
use TSK\ScheduleBundle\Form\Type\AttendanceFormType;
use TSK\ScheduleBundle\Form\Model\Attendance;
use Symfony\Component\HttpFoundation\Response;

class ScheduleAttendanceController extends Controller
{
    /**
     * remove
     * Removes a single attendance records
     * @Route("/admin/schedule/attendance/remove/{id}/{date}", options={"expose"=true})
     * @Template("TSKScheduleBundle:Default:attendance_save.html.twig")
     * @Method("GET")
     */
    public function removeAction(Roster $roster, \DateTime $date)
    {
        $em = $this->getDoctrine()->getManager();
        $attendance = $em->getRepository('TSK\ScheduleBundle\Entity\ScheduleAttendance')->findOneBy(array('student' => $roster->getStudent(), 'class' => $roster->getClass(), 'schedule' => $roster->getSchedule(), 'attDate' => $date));
        if ($attendance) {
            // TODO:  Need try-catch here ...
            $em->getConnection()->beginTransaction();
            $em->remove($attendance);
            $em->flush();
            $em->getConnection()->commit();
            if ($this->getRequest()->isXmlHttpRequest()) {
                return new Response(1);
            } else {
                $this->get('session')->getFlashBag()->add('success', 'Attendance record removed');
            }
        } else {
            return new Response(0);
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * save
     * Saves a collection of attendance records
     * @Route("/admin/schedule/attendance/save", options={"expose"=true})
     * @Template("TSKScheduleBundle:Default:attendance_save.html.twig")
     * @Method("POST")
     */
    public function saveAction(Request $request)
    {
        $form = $this->createForm(new AttendanceFormType(), new Attendance(), array('csrf_protection' => false));

        $form->bind($request);
        $attendance = $form->getData();
        $roster = $attendance->getRosters()->first();

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                // save the attendance
                $em = $this->getDoctrine()->getManager();
                $statuses = $attendance->getStatuses();
                foreach ($attendance->getRosters() as $r) {
                    $sa = new ScheduleAttendance();
                    $sa->setSchool($r->getSchedule()->getSchool());
                    $sa->setClass($r->getClass());
                    $sa->setSchedule($r->getSchedule());
                    $sa->setStudent($r->getStudent());
                    $sa->setAttDate($attendance->getAttDate());
                    $sa->setStatus('present');
                    $em->persist($sa);
                    try {
                        $em->flush();

                    } catch (\Exception $e) {
                        print $e->getMessage();
                        if ($e->getCode() == '23000') {
                            // ignore ...
                        } else {
                            // print $e->getCode();
                            // throw $e;
                        }
                    }
                }
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return new Response(1);
                } else {
                    $this->get('session')->getFlashBag()->add('success', 'Attendance saved!');
                }
            } else {
                $errors = $this->get('validator')->validate($roster);
                $errmsg = '';
                foreach ($errors as $error) {
                    $errmsg = $error->getMessage();
                }
                if ($this->getRequest()->isXmlHttpRequest()) {
                    return new Response('some error');
                } else {
                    $this->get('session')->getFlashBag()->add('error', 'Error saving attendance ' . $errmsg);
                }
            }

            if ($this->getRequest()->isXmlHttpRequest()) {
                return new Response(1);
            } else {
                return $this->redirect($this->getRequest()->headers->get('referer'));
            }
            return $this->redirect($this->generateUrl('tsk_schedule_roster_index', array('id' => $roster->getSchedule()->getId(), 'class' => $roster->getClass()->getId())));
        }
    }
}
