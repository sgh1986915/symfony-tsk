<?php
namespace TSK\StudentBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * StudentRank Listener
 * This listener is called AFTER a student_rank row is written
 * and simply updates the student:rank field
 * 
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class StudentRankListener 
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (in_array(get_class($entity), array('TSK\StudentBundle\Entity\StudentRank'))) {
            $em = $args->getEntityManager();
            
            $dql = 'SELECT s from TSK\StudentBundle\Entity\StudentRank s WHERE s.student=:student ORDER BY s.awardedDate DESC';
            $query = $em->createQuery($dql);
            $query->setParameter('student', $entity->getStudent()->getId());
            $query->setMaxResults(1);
            $rank = $query->getOneOrNullResult();

            if ($rank) {
                $student = $entity->getStudent();
                $student->setRank($rank->getRank());

                $em->persist($student);
                $em->flush();
            }
        }
    }

    public function postUpdate(LifecycleEventArgs $event)
    {
        return $this->postPersist($event);
    }
}
