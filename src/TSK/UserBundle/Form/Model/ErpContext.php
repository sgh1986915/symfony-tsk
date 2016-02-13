<?php
namespace TSK\UserBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

class ErpContext
{
    /**
     * organization 
     * 
     * @Assert\NotBlank()
     * @Assert\Type(type="TSK\UserBundle\Entity\Organization")
     * @var mixed
     * @access private
     */
    private $organization;

    /**
     * school 
     * @Assert\NotBlank()
     * @Assert\Type(type="TSK\SchoolBundle\Entity\School")
     * 
     * @var mixed
     * @access private
     */
    private $school;
 
    /**
     * Get organization.
     *
     * @return organization.
     */
    public function getOrganization()
    {
        return $this->organization;
    }
 
    /**
     * Set organization.
     *
     * @param organization the value to set.
     */
    public function setOrganization($organization)
    {
        $this->organization = $organization;
        return $this;
    }
 
    /**
     * Get school.
     *
     * @return school.
     */
    public function getSchool()
    {
        return $this->school;
    }
 
    /**
     * Set school.
     *
     * @param school the value to set.
     */
    public function setSchool($school)
    {
        $this->school = $school;
        return $this;
    }
}
