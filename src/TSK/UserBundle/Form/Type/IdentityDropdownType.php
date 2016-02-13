<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
// use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\SecurityContext;
use Sonata\AdminBundle\Admin\Pool;
use Doctrine\ORM\EntityRepository;

class IdentityDropdownType extends AbstractType
{
    private $em;
    private $pool;
    private $roles;
    private $securityContext;
    public function __construct(EntityManager $em, Pool $pool, SecurityContext $securityContext, $roles)
    {
        $this->em = $em;
        $this->pool = $pool;
        $this->securityContext = $securityContext;
        $this->roles = $roles;
    }

    public function getName()
    {
        return 'tsk_identity_dropdown';
    }

    public function getParent()
    {
        return 'choice';
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => array('Users' =>  $this->getUsers(), 'Roles' => $this->getRoles()),
            'empty_value' => '-- choose an identity'
        ));
    }

    public function getRoles()
    {
        $r = array();
        $roles = $this->em->getRepository('TSKUserBundle:Role')->findall();
        foreach ($roles as $role) {
            $r[$role->getName()] = $role->getName();
        }
        return $r;
    }

    public function getServiceContainerRoles()
    {
        $roles = array();
        foreach ($this->roles as $name => $rolesHierarchy) {
                $roles[$name] = $name . ': ' . implode(', ', $rolesHierarchy);

                foreach ($rolesHierarchy as $role) {
                    if (!isset($roles[$role])) {
                        $roles[$role] = $role;
                    }
                }
        }
        return $roles;
    }

    public function getSonataAdminRoles()
    {
        $roles = array();
        // Get roles from Sonata Admin classes
        foreach ($this->pool->getAdminServiceIds() as $id) {
            try {
                $admin = $this->pool->getInstance($id);
            } catch (\Exception $e) {
                continue;
            }

            // $isMaster = $admin->isGranted('MASTER');
            $securityHandler = $admin->getSecurityHandler();
            // TODO get the base role from the admin or security handler
            $baseRole = $securityHandler->getBaseRole($admin);

            foreach ($admin->getSecurityInformation() as $role => $permissions) {
                $role = sprintf($baseRole, $role);
                $roles[$role] = $role;
            }
        }
        return $roles;
    }

    public function getUsers()
    {
        $users = array();
        // Filter by organization
        foreach ($this->em->getRepository('TSKUserBundle:User')->findAll() as $u) {
            $users[$u->getUsername()] = $u->getUsername();
        }
        return $users;
    }
}
