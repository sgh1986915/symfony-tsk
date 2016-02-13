<?php
namespace TSK\SchoolBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Event\LifecycleEventArgs;

use TSK\UserBundle\Entity\Role;
/**
 * SchoolListener
 * This listener is called right after a school is created and creates
 * a role for the school based on its id
 * 
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class SchoolListener 
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\SchoolBundle\Entity\School') {
            $role = new Role();
            $role->setOrganization($entity->getContact()->getOrganization());
            $role->setName('ROLE_TSK_SCHOOL_' . $entity->getId());
            $em = $args->getEntityManager();
            $em->persist($role);
            $em->flush();
        }
    }
}
