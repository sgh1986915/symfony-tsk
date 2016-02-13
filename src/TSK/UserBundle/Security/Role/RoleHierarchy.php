<?php
namespace TSK\UserBundle\Security\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy as SymfonyRoleHierarchy;
use Doctrine\ORM\EntityManager;

/**
 * RoleHierarchy 
 * This class works as a service for the security.role_hierarchy service and
 * pulls our roles from the DB.
 * 
 * @uses SymfonyRoleHierarchy
 * @package 
 * @version $id$
 * @copyright 2012 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class RoleHierarchy extends SymfonyRoleHierarchy
{
    private $em;
    private $session;
    private $sessionKey;

    /**
     * @param array $hierarchy
     */
    public function __construct(EntityManager $em, $session='', $sessionKey='')
    {
        $this->em = $em;
        $this->session = $session;
        $this->sessionKey = $sessionKey;

        $hierarchy = $this->buildRolesTree();
        parent::__construct($hierarchy);
    }

    /**
     * Here we build an array with roles. It looks like a two-levelled tree - just 
     * like original Symfony roles are stored in security.yml
     * @return array
     */
    private function buildRolesTree()
    {
        $hierarchy = array();
        // TODO:  Figure out how to inject session into this class, maybe use setter instead of constructor
        // Get current org_id from session
        // $orgId = (int)$this->session->get($this->sessionKey);
        $orgId = 1;
        $org = $this->em->getRepository('TSKUserBundle:Organization')->find($orgId);
        $roleQuery = $this->em->createQuery('SELECT r FROM TSKUserBundle:Role r WHERE r.organization=:org');
        $roleQuery->setParameter(':org', $org);
        $roles = $roleQuery->execute();
        if (count($roles)) {
            foreach ($roles as $role) {
                $parentRoles = $role->getChildRoles();
                if ($parentRoles->count()) {
                    foreach ($parentRoles as $parentRole) {
                        if (!isset($hierarchy[$parentRole->getName()])) {
                            $hierarchy[$parentRole->getName()] = array();
                        }
                        $hierarchy[$parentRole->getName()][] = $role->getName();
                    }
                    if (!isset($hierarchy[$role->getName()])) {
                        $hierarchy[$role->getName()] = array();
                    }
                    $hierarchy[$role->getName()][] = $role->getName();

                } else {
                    if (!isset($hierarchy[$role->getName()])) {
                        $hierarchy[$role->getName()] = array();
                    }
                    $hierarchy[$role->getName()][] = $role->getName();
                }
            }
        } else {
            throw new \Exception('No roles!');
        }
        return $hierarchy;
    }
}
