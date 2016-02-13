<?php

namespace TSK\UserBundle\Security\Handler;

class TSKAclSecurityHandler extends AclSecurityHandler implements AclSecurityHandlerInterface
{
    public function createObjectSecurity(AdminInterface $admin, $object)
    {
        // retrieving the ACL for the object identity
        $objectIdentity = ObjectIdentity::fromDomainObject($object);
        $acl            = $this->getObjectAcl($objectIdentity);
        if (is_null($acl)) {
            $acl = $this->createAcl($objectIdentity);
        }

        // retrieving the security identity of the currently logged-in school!
        $school = $this->securityContext->getToken()->getUser()->getContact()->getActiveSchool();
        $securityIdentity = UserSecurityIdentity::fromAccount($school);

        $this->addObjectOwner($acl, $securityIdentity);
        $this->addObjectClassAces($acl, $this->buildSecurityInformation($admin));
        $this->updateAcl($acl);
    }
}
