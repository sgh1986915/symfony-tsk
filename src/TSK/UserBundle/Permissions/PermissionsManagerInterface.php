<?php
namespace TSK\UserBundle\Permissions;

interface PermissionsManagerInterface
{

    /**
     * getPermissionsForIdentity 
     * 
     * @param mixed $identity 
     * @param mixed $identityType 
     * @access public
     * @return void
     */
    public function getPermissionsForIdentity($identity, $identityType);

    /**
     * savePermissionsForIdentity 
     * 
     * @param mixed $identity 
     * @param mixed $identityType 
     * @param mixed $permissions 
     * @access public
     * @return void
     */
    public function savePermissionsForIdentity($identity, $identityType, $permissions);

    /**
     * getResources 
     * 
     * @access public
     * @return void
     */
    public function getResources();
}
