<?php
namespace TSK\StudentBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * StatusUpdateListener 
 * This listener simply initializes a student's status to 'active' 
 * when the student is first created.
 *
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class StatusUpdateListener 
{
    private $session;
    private $sessionKey;

    public function __construct(Session $session, $sessionKey)
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
    }


    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        
        if (in_array(get_class($entity), array('TSK\StudentBundle\Entity\Student'))) {
            $entityManager = $args->getEntityManager();
            if (!$entity->getStudentStatus()) {
                $studentStatus = $entityManager->getRepository('TSK\StudentBundle\Entity\StudentStatus')->findOneByName(array('organization' => $this->session->get($this->sessionKey), 'name' => 'active'));
                if ($studentStatus) {
                    $entity->setStudentStatus($studentStatus);
                }
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $event)
    {
        return $this->prePersist($event);
    }
}
