<?php
namespace TSK\ScheduleBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use TSK\StudentBundle\Entity\StudentCreditLog;
use TSK\StudentBundle\Event\StudentEvents;
use TSK\StudentBundle\Event\StudentProgressEvent;

class ScheduleAttendanceListener 
{
    private $dispatcher;

    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * postPersist 
     * After saving a ScheduleAttendance record we need to add
     * the required class credits to the student's credit log.
     * 
     * @param LifecycleEventArgs $args 
     * @access public
     * @return void
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\ScheduleBundle\Entity\ScheduleAttendance') {
            // add to student_credit_log
            $em = $args->getEntityManager();

            $ctcs = $entity->getClass()->getClassTypeCredits();
            if (count($ctcs)) {
                foreach ($ctcs as $ctc) {
                    if ($ctc->getValue()) {
                        $scl = new StudentCreditLog();
                        $scl->setStudent($entity->getStudent());
                        $scl->setClassType($ctc->getClassType());
                        $scl->setAttendance($entity);
                        $scl->setValue($ctc->getValue());
                        $em->persist($scl);
                    }
                }
                $em->flush();
            }

            // Dispatch student progress event ...
            $studentProgressEvent = new StudentProgressEvent($entity->getStudent());
            $this->dispatcher->dispatch(StudentEvents::STUDENT_PROGRESS, $studentProgressEvent);
/*

            // debit to contract_token_debit_log
            // Look up contract by student and school
            // if contract_num_tokens > 0
                $school = $entity->getSchool();
                $student = $entity->getStudent();
                $contractRepo = $em->getRepository('TSK\ContractBundle\Entity\Contract');
                // $contractRepo
                // then we must debit
            // else
                //do nothing
*/
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        return $this->postPersist($args);
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\ScheduleBundle\Entity\ScheduleAttendance') {
            // Dispatch student progress event ...
            $studentProgressEvent = new StudentProgressEvent($entity->getStudent());
            $this->dispatcher->dispatch(StudentEvents::STUDENT_PROGRESS, $studentProgressEvent);
        }
    }
}
