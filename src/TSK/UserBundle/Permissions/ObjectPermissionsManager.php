<?php
namespace TSK\UserBundle\Permissions;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Sonata\AdminBundle\Admin\Pool as SonataAdminPool;
use TSK\UserBundle\Form\Model\Permission;
use TSK\UserBundle\Permissions\PermissionsManagerInterface;
use Doctrine\DBAL\Connection;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\Exception as AclException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Acl\Domain\PermissionGrantingStrategy;
use Symfony\Component\Security\Acl\Domain\Acl;
use Doctrine\ORM\EntityManager;


class ObjectPermissionsManager implements PermissionsManagerInterface
{
    private $connection;
    private $aclProvider;
    private $em;

    public function __construct(EntityManager $em, AclProviderInterface $aclProvider)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
        $this->aclProvider = $aclProvider;
    }

    /**
     * getPermissionsForIdentity 
     * 
     * @param mixed $identity 
     * @param mixed $identityType 
     * @access public
     * @return array of Permission objs
     */
    public function getPermissionsForIdentity($identity, $identityType)
    {
        $identityType = strtolower($identityType);

        if ($identityType == 'users') {
            $aces = $this->getUserAces($identity);
        } elseif ($identityType == 'roles') {
            $aces = $this->getRoleAces($identity);
        } else {
            throw new \Exception('invalid identityType '. $identityType);
        } 

        foreach ($this->getResources() as $entity) {

            $allowedEntities = array(
                    'TSK\UserBundle\Entity\Contact',
                    // 'TSK\UserBundle\Entity\Organization',
                    );
            if (in_array($entity->name, $allowedEntities)) {
                $fieldNames = $this->em->getClassMetaData($entity->name)->getFieldNames();

                // do fields
                foreach ($fieldNames as $fieldName) {
                    $entity_name = preg_replace('/\\\/', '_', $entity->name);
                    $class = $entity->name;
                    $alias = $entity_name;
                    
                    $fieldMask = array();
                    foreach ($aces as $idx => $arr) {
                        if (($arr['object_identifier'] == $class) && ($arr['field_name'] == $fieldName)) {
                            $fieldMask = $arr['mask'];
                            break;
                        }
                    }
                    $permissions[] = new Permission(
                            $class, 
                            $alias,
                            $fieldName, 
                            $this->arrayifyBitMask($fieldMask));
                }
            }

        }
        return $permissions;
    }

    /**
     * savePermissionsForIdentity 
     * 
     * @param mixed $identity 
     * @param mixed $identityType 
     * @param mixed $permissions 
     * @access public
     * @return void
     */
    public function savePermissionsForIdentity($identity, $identityType, $permissions)
    {
        // delete all permissions for identity
        switch(strtolower($identityType)) {
            case 'users':
                $securityIdentity = new UserSecurityIdentity($identity, 'TSK\UserBundle\Entity\User');
            break;

            case 'roles':
                $securityIdentity = new RoleSecurityIdentity($identity);
            break;
    
            default:
                throw new \Exception("Invalid identity_type $identity_type");
            break;
        }

        $aclProvider = $this->aclProvider;
        foreach ($permissions as $idx => $perm) {
            $objectIdentity = new ObjectIdentity($perm->getClassName(), $perm->getClassType());

            $builder = new MaskBuilder();
            $builder->add(0);
            foreach ($perm->getBits() as $idx => $permission) {
                if ($permission) {
                    $builder->add($permission);
                }
            }

            try {
                $acl = $aclProvider->findAcl($objectIdentity);
            } catch (AclException $e) {
                $acl = $aclProvider->createAcl($objectIdentity);
            }

            // If we already have Access Control Entries for this object AND user
            // We do an update, otherwise insert.  
            $classAces = $acl->getClassFieldAces($perm->getFieldName());
            if (count($classAces)) {
                $doClassUpdate = 0;
                foreach ($classAces as $idx => $ca) {
                    if ($ca->getSecurityIdentity() instanceof UserSecurityIdentity && $ca->getSecurityIdentity()->getUsername() === $identity && $ca->getAcl()->getObjectIdentity()->getIdentifier() == $acl->getObjectIdentity()->getIdentifier()) {
                        $doClassUpdate = 1;
                        break;
                    }
                    if ($ca->getSecurityIdentity() instanceof RoleSecurityIdentity && $ca->getSecurityIdentity()->getRole() === $identity && $ca->getAcl()->getObjectIdentity()->getIdentifier() == $acl->getObjectIdentity()->getIdentifier()) {
                        $doClassUpdate = 1;
                        break;
                    }
                }
                if ($doClassUpdate) {
                    $acl->updateClassFieldAce($idx, $perm->getFieldName(), $builder->get());
                } else {
                    $acl->insertClassFieldAce($perm->getFieldName(), $securityIdentity, $builder->get());
                }
            } else {
                $acl->insertClassFieldAce($perm->getFieldName(), $securityIdentity, $builder->get());
            }
            $aclProvider->updateAcl($acl);
        }
    }

    /**
     * getResources 
     * 
     * @access public
     * @return array
     */
    public function getResources()
    {
        return $this->em->getMetaDataFactory()->getAllMetadata();
    }

    private function getUserAces($user)
    {
        $identity = 'TSK\UserBundle\Entity\User-' . $user;
        $dbh = $this->connection;
        $stmt = $dbh->prepare('select o.object_identifier, c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_object_identities o on o.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity');
        $stmt->bindValue(':identity', $identity);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function getRoleAces($role)
    {
        $dbh = $this->connection;
        $stmt = $dbh->prepare('select o.object_identifier, c.class_type, e.field_name, e.mask from acl_classes c inner join acl_entries e on e.class_id=c.id inner join acl_object_identities o on o.class_id=c.id inner join acl_security_identities s on e.security_identity_id=s.id WHERE s.identifier=:identity');
        $stmt->bindValue(':identity', $role);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * arrayifyBitMask 
     * Converts a bitmask into an array of permission strings
     * 
     * @param int $mask 
     * @access private
     * @return void
     */
    private function arrayifyBitMask($mask)
    {
        $mask = (int) $mask;
        $perms = array();
        // Important:  Leave in those bad permission placeholders!!
        $bits = array('view','create','edit','delete','undelete','operator','master','owner','foo','bar','boof','baf','list','export');
        foreach ($bits as $idx => $name) {
            if ((1 << $idx) & $mask) {
                $perms[] = $name;
            }
        }
        return $perms;
    }

}
