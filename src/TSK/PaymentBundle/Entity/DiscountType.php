<?php

namespace TSK\PaymentBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Discount
 *
 * @ORM\Table(name="tsk_ref_discount_type")
 * @ORM\Entity
 */
class DiscountType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="discount_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\UserBundle\Entity\Organization", cascade={"persist"})
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
     * @var string
     *
     * @ORM\Column(name="discount_type", type="string", length=255, nullable=true)
     */
    private $discountType;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float")
    private $amount;
     */

    public function setId($id)
    {
        $this->id = $id;
        return $this;
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
     * @return Discount
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

    /**
     * Set discountType
     *
     * @param string $discountType
     * @return Discount
     */
    public function setDiscountType($discountType)
    {
        $this->discountType = $discountType;

        return $this;
    }

    /**
     * Get discountType
     *
     * @return string 
     */
    public function getDiscountType()
    {
        return $this->discountType;
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return Discount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    public function __toString()
    {
        return (string) $this->getName();
    }
 
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
}
