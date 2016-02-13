<?php
namespace TSK\SchoolBundle\EventListener;

use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\Event\LifecycleEventArgs;

use TSK\UserBundle\Entity\Role;
/**
 * OrgRole
 * This listener is called right after an organization is created and creates
 * a role for the organization based on its id
 * 
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class OrgRoleListener 
{
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (get_class($entity) == 'TSK\UserBundle\Entity\Organization') {
            $role = new Role();
            $role->setOrganization($entity);
            $role->setName('ROLE_TSK_ORG_' . $entity->getId());
            $em = $args->getEntityManager();
            $em->persist($role);
            $em->flush();
        }
    }
}
