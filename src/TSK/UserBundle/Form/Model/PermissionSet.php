<?php
namespace TSK\UserBundle\Form\Model;

use Doctrine\Common\Collections\ArrayCollection;

class PermissionSet
{
    private $permissionType;
    private $identity;
    private $identityType;
    private $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }
 
    /**
     * Get permissionType.
     *
     * @return permissionType.
     */
    public function getPermissionType()
    {
        return $this->permissionType;
    }
 
    /**
     * Set permissionType.
     *
     * @param permissionType the value to set.
     */
    public function setPermissionType($permissionType)
    {
        $this->permissionType = $permissionType;
        return $this;
    }
 
    /**
     * Get identity.
     *
     * @return identity.
     */
    public function getIdentity()
    {
        return $this->identity;
    }
 
    /**
     * Set identity.
     *
     * @param identity the value to set.
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }
 
    /**
     * Get identityType.
     *
     * @return identityType.
     */
    public function getIdentityType()
    {
        return $this->identityType;
    }
 
    /**
     * Set identityType.
     *
     * @param identityType the value to set.
     */
    public function setIdentityType($identityType)
    {
        $this->identityType = $identityType;
        return $this;
    }
 
    /**
     * Get permissions.
     *
     * @return permissions.
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
 
    /**
     * Set permissions.
     *
     * @param permissions the value to set.
     */
    public function setPermissions($permissions)
    {
        if ($permissions) {
            foreach ($permissions as $permission) {
                $this->addPermission($permission);
            }
        }
        return $this;
    }

    public function addPermission(Permission $permission)
    {
        $this->permissions[] = $permission;
    }

    public function removePermission(Permission $permission)
    {
        return $this->permissions->removeElement($permission);
    }
}
