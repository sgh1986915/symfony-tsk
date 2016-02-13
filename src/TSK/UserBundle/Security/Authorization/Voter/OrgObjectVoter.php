<?php
namespace TSK\UserBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Acl\Voter\AclVoter;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Security\Acl\Voter\FieldVote;
use Symfony\Component\HttpKernel\Log\LoggerInterface;
use Symfony\Component\Security\Acl\Exception\NoAceFoundException;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\AclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Acl\Model\SecurityIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityRetrievalStrategyInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;



/**
 * OrgObjectVoter 
 * The purpose of this class is to sniff the current user's org_id
 * and verify that the object they're viewing has a role permission of
 * ROLE_TSK_ORG_<org_id>.  If we do not have it we DENY permission
 * explicitly.
 * 
 * @uses AclVoter
 * @package 
 * @version $id$
 * @copyright 2012 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class OrgObjectVoter extends AclVoter
{
    private $session;
    private $sessionKey;
    private $permissionMap;
    private $protectedClasses;
    private $securityIdentityRetrievalStrategy;
    private $aclProvider;

    public function __construct($session, $sessionKey, array $protectedClasses, PermissionMapInterface $permissionMap, SecurityIdentityRetrievalStrategyInterface $securityIdentityRetrievalStrategy, ObjectIdentityRetrievalStrategyInterface $objectIdentityRetrievalStrategy, $aclProvider)
    {
        $this->protectedClasses = $protectedClasses;
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->permissionMap = $permissionMap;
        $this->securityIdentityRetrievalStrategy = $securityIdentityRetrievalStrategy;
        $this->objectIdentityRetrievalStrategy = $objectIdentityRetrievalStrategy;
        $this->aclProvider = $aclProvider;
    }


    /**
    * {@InheritDoc}
    */
    public function supportsClass($class)
    {
        return in_array($class, $this->protectedClasses);
    }

    public function supportsAttribute($attribute)
    {
        return $this->permissionMap->contains($attribute);
    }

    /**
     * vote 
     * 
     * @param TokenInterface $token 
     * @param mixed $object 
     * @param array $attributes 
     * @access public
     * @return void
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!$this->supportsClass(get_class($object))) {
                return self::ACCESS_ABSTAIN;
            }

            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
            // grab org object from session
            $org = $this->session->get($this->sessionKey);
            if (!$org) {
                throw new \Exception('Invalid org id, you should not be here');
            }
            // build role_string
            $orgRoleName = sprintf('ROLE_TSK_ORG_%d', $org->getId());
            $orgRole = new RoleSecurityIdentity($orgRoleName);

            if ($object instanceof ObjectIdentityInterface) {
                $oid = $object;
            } elseif (null === $oid = $this->objectIdentityRetrievalStrategy->getObjectIdentity($object)) {
                if (null !== $this->logger) {
                    $this->logger->debug(sprintf('Object identity unavailable. Voting to %s', $this->allowIfObjectIdentityUnavailable? 'grant access' : 'abstain'));
                }
                return self::ACCESS_DENIED;
            }

            // verify that current user has role $role
            // $sids = $this->securityIdentityRetrievalStrategy->getSecurityIdentities($token);
            // ld($sids);
            try {
                $masks = $this->permissionMap->getMasks((string) $attribute, $object);
                $acl = $this->aclProvider->findAcl($oid, array($orgRole));

                if ($acl->isGranted($masks, array($orgRole), false)) {
                    return self::ACCESS_GRANTED;
                }
        
            } catch (\Exception $e) {
                return self::ACCESS_DENIED;
                // throw $e;
            }
        }
        return self::ACCESS_DENIED;
    }
}
