<?php
namespace TSK\UserBundle\Security\Handler;

use Sonata\AdminBundle\Security\Handler\AclSecurityHandler;
use Sonata\AdminBundle\Admin\AdminInterface;

class TSKAclSecurityHandler extends AclSecurityHandler 
{
    public function createObjectSecurity(AdminInterface $admin, $object)
    {
        // nada!
    }
}
