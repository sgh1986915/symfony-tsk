<?php
namespace TSK\UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use TSK\UserBundle\Entity\Role;


/**
 * SchoolListener
 * This listener is called right after an entity is created and ties
 * the entity to both a school via acl to ROLE_TSK_SCHOOL_<school_id>
 * and an org via acl to ROLE_TSK_ORG_<org_id>
 * 
 * 
 * @package 
 * @version $id$
 * @copyright 2012 TSK
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class SchoolAclListener 
{
    private $session;
    private $orgSessionKey;
    private $schoolSessionKey;

    public function __construct($session, $orgSessionKey, $schoolSessionKey)
    {
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
        $this->schoolSessionKey = $schoolSessionKey;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $org    = $this->session->get($this->orgSessionKey);
        if (!$org) {
            return false;
        }
        $entity = $args->getEntity();
        $className = get_class($entity);

        if ($className == 'TSK\UserBundle\Entity\Contact') {
            $org    = $this->session->get($this->orgSessionKey);
            $school = $this->session->get($this->schoolSessionKey);
            $orgRole = sprintf('ROLE_TSK_ORG_%d', $org);
            $schoolRole = sprintf('ROLE_TSK_SCHOOL_%d', $school);
            $conn = $args->getEntityManager()->getConnection();

            $builder = new MaskBuilder();
            $builder->add('OWNER');
            // $builder->add('EDIT');
            // $builder->add('LIST');
            // $builder->add('LIST');
            $mask = $builder->get();

            try {
                $conn->beginTransaction();
                $this->saveAcl($conn, $entity, $orgRole, $className, $mask, 0);
                $this->saveAcl($conn, $entity, $schoolRole, $className, $mask, 1);
                // $securityIdentityID = $this->createSecurityIdentity($conn, $schoolRole);
                // $classID = $this->createClassEntry($conn, $className);
                // $objectIdentityID = $this->createObjectIdentity($conn, $classID, $entity->getId());
                // $this->createAclEntry($conn, $classID, $objectIdentityID, $securityIdentityID, $mask);
                // $this->createObjectIdentityAncestor($conn, $objectIdentityID, $objectIdentityID);
                $conn->commit();
            } catch (\Exception $e) {
                $conn->rollback();
                throw $e;
            }
        }
    }

    public function saveAcl($conn, $entity, $role, $className, $mask, $aceOrder=0)
    {
        $securityIdentityID = $this->createSecurityIdentity($conn, $role);
        $classID = $this->createClassEntry($conn, $className);
        $objectIdentityID = $this->createObjectIdentity($conn, $classID, $entity->getId());
        $this->createAclEntry($conn, $classID, $objectIdentityID, $securityIdentityID, $mask, $aceOrder);
        $this->createObjectIdentityAncestor($conn, $objectIdentityID, $objectIdentityID);
    }


    public function createSecurityIdentity($conn, $role)
    {
        $sql = 'insert into acl_security_identities (id, identifier, username) VALUES (NULL, :role, 0) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function createClassEntry($conn, $class)
    {
        $sql = 'insert into acl_classes (class_type) values (:class) ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':class', $class);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function createObjectIdentity($conn, $classId, $entityId)
    {
        $sql = 'insert into acl_object_identities values (NULL, NULL, :classId, :entityId, 1) on DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':classId', $classId);
        $stmt->bindValue(':entityId', $entityId);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function createAclEntry($conn, $aclClassesId, $objectIdentityId, $securityIdentityId, $mask, $aceOrder=0)
    {
        $sql = 'insert into acl_entries (id, class_id, object_identity_id, security_identity_id, field_name, ace_order, mask, granting, granting_strategy, audit_success, audit_failure) values (NULL, :aclClassesId, :objectIdentityId, :securityIdentityId, NULL, :aceOrder, :mask, 1, "all", 0, 0)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':aclClassesId', $aclClassesId);
        $stmt->bindValue(':objectIdentityId', $objectIdentityId);
        $stmt->bindValue(':securityIdentityId', $securityIdentityId);
        $stmt->bindValue(':mask', $mask);
        $stmt->bindValue(':aceOrder', $aceOrder);
        $stmt->execute();
        return $conn->lastInsertId();
    }

    public function createObjectIdentityAncestor($conn, $objectIdentityId, $ancestorId)
    {
        $sql = 'insert ignore into acl_object_identity_ancestors (object_identity_id, ancestor_id) values (:object_identity_id, :ancestor_id)';
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':object_identity_id', $objectIdentityId);
        $stmt->bindValue(':ancestor_id', $ancestorId);
        $stmt->execute();
        return true;
    }


}
