<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Role
 *
 * @ORM\Table(name="tsk_role", uniqueConstraints={@ORM\UniqueConstraint(name="role_org_uniq", columns={"fk_org_id", "name"})})
 * @ORM\Entity
 */
class Role
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Organization", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_org_id", referencedColumnName="org_id", nullable=false, unique=false)
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     */
    protected $organization;


    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
    private $parent;
     **/


    /**
     * @ORM\ManyToMany(targetEntity="Role", mappedBy="parentRoles")
     */
    private $childRoles;

    /**
     * @ORM\ManyToMany(targetEntity="Role", inversedBy="childRoles", cascade="persist")
     * @ORM\JoinTable(name="tsk_role_parents",
     *      joinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_parent_id", referencedColumnName="id")}
     *      )
     */
    private $parentRoles;

    public function __construct()
    {
        $this->parentRoles = new ArrayCollection();
        $this->childRoles = new ArrayCollection();
    }

    /**
     * setId 
     * 
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Role
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    public function getParentRoles()
    {
        return $this->parentRoles;
    }

    public function setParentRoles($parentRoles)
    {
        foreach ($parentRoles as $role) {
            $this->addParentRole($role);
        }
    }

    public function addParentRole(Role $role)
    {
        $this->parentRoles[] = $role;
    }

    public function removeParentRole(Role $role)
    {
        return $this->parentRoles->removeElement($role);
    }

    public function getChildRoles()
    {
        return $this->childRoles;
    }

    public function setChildRoles($childRoles)
    {
        foreach ($childRoles as $role) {
            $this->addChildRole($role);
        }
    }

    public function addChildRole(Role $role)
    {
        $this->childRoles[] = $role;
    }

    public function removeChildRole(Role $role)
    {
        return $this->childRoles->removeElement($role);
    }

    public function setOrganization(Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function getOrganization()
    {
        return $this->organization;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
}
