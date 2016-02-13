<?php
namespace TSK\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\DBAL\DBALException;

use TSK\ScheduleBundle\Entity\ScheduleEntity;
use TSK\ScheduleBundle\Entity\ScheduleAttendance;
use TSK\ScheduleBundle\Form\Model\Attendance;
use TSK\ScheduleBundle\Entity\Roster;
use TSK\ScheduleBundle\Form\Type\RosterFormType;
use TSK\ScheduleBundle\Form\Type\AttendanceFormType;
use TSK\ClassBundle\Entity\Classes;
use TSK\StudentBundle\Entity\Student;
use APY\DataGridBundle\Grid\Source\Vector;
use APY\DataGridBundle\Grid\Columns;
use APY\DataGridBundle\Grid\Column\NumberColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;
use APY\DataGridBundle\Grid\Column;


use APY\DataGridBundle\Grid\Export\ExcelExport;
use APY\DataGridBundle\Grid\Export\DSVExport;
use APY\DataGridBundle\Grid\Export\JSONExport;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Action\RowAction;


class RosterController extends Controller
{
    /**
     * index
     * manage class roster based on scheduleentity
     *
     * @Route("/admin/schedule/{id}/roster/{class}/{date}")
     * @Template("TSKScheduleBundle:Default:roster2.html.twig")
     * @Method({"GET"})
     */
    public function indexAction(ScheduleEntity $scheduleEntity, Classes $class, \DateTime $date)
    {
        // TODO:  Need to verify that scheduleEntity even exists as of $date
        // verify that scheduleEntity has a scheduleInstance on date
        $em = $this->getDoctrine()->getManager();
        $rosters = $this->getRoster($scheduleEntity, $date);

        $roster = new Roster();
        $roster->setClass($class);
        $roster->setSchedule($scheduleEntity);
        $roster->setStart($date);

        $form = $this->createForm(new RosterFormType($this->get('tsk.admin.student'), 'tsk.admin.student'), $roster);

        return array(
                'class' => $class, 
                'scheduleEntity' => $scheduleEntity, 
                'rosters' => $rosters, 
                'date' => $date->format('Y-m-d'), 
                'form' => $form->createView());
    }

    public function hasRosterOnDate($scheduleEntity, $date)
    {
        $em = $this->getDoctrine()->getManager();
        $sql = 'select date(start) from tsk_schedule_instance si where si.fk_schedule_entity_id=:schedule_entity_id and date(start)=:date';
        $conn = $em->getConnection();
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(":schedule_entity_id", $scheduleEntity->getId());
        $stmt->bindValue(":date", $date->format('Y-m-d'), \PDO::PARAM_STR);
        $stmt->execute();

        $rows = array();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $rows[] = $result;
        }
        return count($rows) > 0;
    }

    public function getRoster($scheduleEntity, $date)
    {
        if ($this->hasRosterOnDate($scheduleEntity, $date)) {
            $em = $this->getDoctrine()->getManager();
             // Need to find students on roster as of this date ...
            $sql = 'select r.*, rank.description as rnk, c.first_name, c.last_name, a.att_date, a.status from tsk_schedule_roster r left join tsk_schedule_attendance a on r.fk_class_id=a.fk_class_id and r.fk_student_id=a.fk_student_id and r.fk_schedule_entity_id=a.fk_schedule_entity_id and a.att_date=:att_date1 inner join tsk_student s on r.fk_student_id=s.student_id inner join tsk_contact c on s.fk_contact_id=c.contact_id inner join tsk_ref_rank rank on rank.rank_id=s.fk_rank_id where :att_date2 between r.start and r.until and r.fk_schedule_entity_id=:schedule_entity_id';
            $conn = $em->getConnection();

            $stmt = $conn->prepare($sql);
            $stmt->bindValue(":att_date1", $date->format('Y-m-d'));
            $stmt->bindValue(":att_date2", $date->format('Y-m-d'));
            $stmt->bindValue(":schedule_entity_id", $scheduleEntity->getId());
            $stmt->execute();

            $rows = array();
            while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $rows[] = $result;
            }
            return $rows;
        } else {
            throw new \InvalidArgumentException('Roster does not exist on this date');
            return NULL;
        }
    }

    /**
     * index
     * manage class roster based on scheduleentity
     *
     * @Route("/admin/schedule/{id}/roster/{class}/remove/{student}")
     * @Template("TSKScheduleBundle:Default:roster.html.twig")
     * @Method({"GET"})
     */
    public function removeAction(ScheduleEntity $scheduleEntity, Classes $class, Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $roster = $em->getRepository('TSK\ScheduleBundle\Entity\Roster')->findOneBy(array('schedule' => $scheduleEntity, 'class' => $class, 'student' => $student));
        if (!$roster) {
            $this->get('session')->setFlash('sonata_flash_error', 'Student not found on roster');
        } else {
            $em->remove($roster);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'Student removed from roster');
        }
        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * save
     * save roster info
     *
     * @Route("/admin/schedule/roster/save")
     * @Template("TSKScheduleBundle:Default:roster.html.twig")
     * @Method({"POST"})
     */
    public function saveAction(Request $request)
    {
        $roster = new Roster();
        $form = $this->createForm(new RosterFormType($this->get('tsk.admin.student'), 'tsk.admin.student'), $roster);
        $form->bind($request);

        if ($request->isMethod('POST')) {
            if ($form->isValid()) {
                // lets save
                $em = $this->getDoctrine()->getManager();
                $student = $roster->getStudent();
                // check student status
                if (!in_array($student->getStudentStatus()->getName(), array('active', 'trial'))) {
                    $this->get('session')->setFlash('sonata_flash_error', 'Student is not in good standing');
                } else {
                    $activeContract = $student->getActiveContract();
                    if (!$activeContract) {
                        throw new \Exception('No active contract for student');
                    }
                    $today = new \DateTime();
                    if ($activeContract->getContractExpiry() > $today) {
                        $roster->setUntil($activeContract->getContractExpiry());
                        try {
                            $em->persist($roster);
                            $em->flush();
                            $this->get('session')->getFlashBag()->add('success', 'Student added to roster');
                        } catch (DBALException $e) {
                            if ($e->getPrevious()->getCode() == '23000') {
                                $this->get('session')->getFlashBag()->add('notice', 'Student already added to roster');
                            } else {
                                throw $e;
                            }
                        }
                    } else {
                        $this->get('session')->setFlash('sonata_flash_error', 'Student contract has expired as of ' . $activeContract->getContractExpiry()->format('Y-m-d'));
                    }
                }
            } else {
                $errors = $this->get('validator')->validate($roster);
                foreach ($errors as $error) {
                    $errmsg = $error->getMessage();
                }
                $this->get('session')->getFlashBag()->add('error', 'Error saving student to roster ' . $errmsg);
            }
        }

        return $this->redirect($this->getRequest()->headers->get('referer'));
    }

    /**
     * index
     * manage class roster based on scheduleentity
     *
     * @Route("/admin/schedule/{id}/rostergrid/")
     * @Template("TSKScheduleBundle:Default:grid.html.twig")
     * @Method({"GET","POST"})
     */
    public function testAction(ScheduleEntity $scheduleEntity)
    {
        $date = $this->getRequest()->get('date');
        $class = $this->getRequest()->get('class');
        if (!$date instanceof \DateTime) {
            $date = new \DateTime($date);
        }
        $em = $this->getDoctrine()->getManager();
        $rosters = $this->getRoster($scheduleEntity, $date);
        $columns = array(
            new Column\NumberColumn(array('id' => 'roster_id', 'primary' => true, 'title' => 'Roster Id', 'source' => true, 'field'=>'roster_id')),
        );
        $source = new Vector($rosters);
        $grid = $this->get('grid');
        $grid->setSource($source);
        return $grid->getGridResponse();
    }
}
