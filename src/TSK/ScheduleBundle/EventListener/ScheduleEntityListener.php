<?php
namespace TSK\ScheduleBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;


class ScheduleEntityListener 
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\ScheduleBundle\Entity\ScheduleEntity') {
            // check for user
            if (!$entity->getUser()) {
                $user = $this->container->get('security.context')->getToken()->getUser();
                if (!$user) {
                    throw new \Exception('Unable to get user');
                }
                $entity->setUser($user);
            }

            // check for school
            if (!$entity->getSchool()) {
                $em = $args->getEntityManager();
                $schoolSessionKey = $this->container->getParameter('tsk_user.session.school_key');
                $schoolID = $this->container->get('session')->get($schoolSessionKey);
                $school = $em->getRepository('TSK\SchoolBundle\Entity\School')->findOneById($schoolID);
                if (!$school) {
                    throw new \Exception('Invalid school id in session');
                }
                $entity->setSchool($school);
            }
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        return $this->prePersist($args);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\ScheduleBundle\Entity\ScheduleEntity') {
            $processor = $this->container->get('tsk_schedule.processor.schedule_instances');
            $processor->processEntity($entity);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        return $this->postPersist($args);
    }
}
