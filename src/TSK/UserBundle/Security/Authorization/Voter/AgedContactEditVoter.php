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
 * AgedContactEditVoter 
 * The purpose of this class is to prevent ROLE_ADMIN users from editing
 * contact records older than 30 days old.
 * 
 * @uses AclVoter
 * @package 
 * @version $id$
 * @copyright 2012 Obverse Dev
 * @author Malaney J. Hill <malaney@gmail.com> 
 * @license PHP Version 3.01 {@link http://www.php.net/license/3_01.txt}
 */
class AgedContactEditVoter extends AclVoter
{
    private $securityIdentityRetrievalStrategy;
    private $maxAgeSeconds;

    public function __construct(SecurityIdentityRetrievalStrategyInterface $securityIdentityRetrievalStrategy, $maxAgeSeconds=60)
    {
        $this->maxAgeSeconds = $maxAgeSeconds;
        $this->securityIdentityRetrievalStrategy = $securityIdentityRetrievalStrategy;
    }

    /**
    * {@InheritDoc}
    */
    public function supportsClass($class)
    {
        return $class == 'TSK\UserBundle\Entity\Contact';
    }

    public function supportsAttribute($attribute)
    {
        return $attribute == 'EDIT';
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
        // if this user has ROLE_ORG_ADMIN or ROLE_SUPER_ADMIN then grant immediately
        $sids = $this->securityIdentityRetrievalStrategy->getSecurityIdentities($token);
        foreach ($sids as $sid) {
            if ($sid instanceof RoleSecurityIdentity) {
                if ($sid->equals(new RoleSecurityIdentity('ROLE_ORG_ADMIN')) ||
                    $sid->equals(new RoleSecurityIdentity('ROLE_SUPER_ADMIN'))
                ) {
                    return self::ACCESS_GRANTED;
                }
            }
        }
        
        foreach ($attributes as $attribute) {
            if (!$this->supportsClass(get_class($object))) {
                return self::ACCESS_ABSTAIN;
            }

            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
 
            $now = new \DateTime();

            $ageSeconds = $now->getTimestamp() - $object->getCreatedDate()->getTimestamp();
            // print $ageSeconds;
            return ($ageSeconds > $this->maxAgeSeconds) ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
        }
    }
}
