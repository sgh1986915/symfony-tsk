<?php

namespace TSK\RankBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Rank
 *
 * @ORM\Table(name="tsk_ref_rank")
 * @ORM\Entity
 */
class Rank
{
    /**
     * @var integer
     *
     * @ORM\Column(name="rank_id", type="integer")
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
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\RankBundle\Entity\RankType", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_rank_type_id", referencedColumnName="rank_type_id", nullable=true)
     * @Assert\Type(type="TSK\RankBundle\Entity\RankType")
     */
    private $rankType;


    /**
     * @var integer
     *
     * @ORM\Column(name="rank_order", type="smallint")
     */
    private $rankOrder;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=100, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="full_description", type="string", length=255, nullable=true)
     */
    private $fullDescription;

    /**
     * @var integer
     *
     * @ORM\Column(name="kb_stripe", type="smallint", nullable=true)
     */
    private $kbStripe;

    /**
     * @var integer
     *
     * @ORM\Column(name="gr_stripe", type="smallint", nullable=true)
     */
    private $grStripe;

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
     * @return Rank
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
     * Set rankOrder
     *
     * @param integer $rankOrder
     * @return Rank
     */
    public function setRankOrder($rankOrder)
    {
        $this->rankOrder = $rankOrder;
    
        return $this;
    }

    /**
     * Get rankOrder
     *
     * @return integer 
     */
    public function getRankOrder()
    {
        return $this->rankOrder;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Rank
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function getOrganization()
    {
        return $this->organization;
    }
    
    public function setOrganization(\TSK\UserBundle\Entity\Organization $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    public function __toString()
    {
        return (string)  '(' . $this->getId() . ') ' . $this->fullDescription;
    }

    /**
     * Get kbStripe.
     *
     * @return kbStripe.
     */
    public function getKbStripe()
    {
        return $this->kbStripe;
    }
 
    /**
     * Set kbStripe.
     *
     * @param kbStripe the value to set.
     */
    public function setKbStripe($kbStripe)
    {
        $this->kbStripe = $kbStripe;
        return $this;
    }
 
    /**
     * Get grStripe.
     *
     * @return grStripe.
     */
    public function getGrStripe()
    {
        return $this->grStripe;
    }
 
    /**
     * Set grStripe.
     *
     * @param grStripe the value to set.
     */
    public function setGrStripe($grStripe)
    {
        $this->grStripe = $grStripe;
        return $this;
    }

    /**
     * Get rankType.
     *
     * @return rankType.
     */
    public function getRankType()
    {
        return $this->rankType;
    }
 
    /**
     * Set rankType.
     *
     * @param rankType the value to set.
     */
    public function setRankType(RankType $rankType)
    {
        $this->rankType = $rankType;
        return $this;
    }

    /**
     * Get fullDescription.
     *
     * @return fullDescription.
     */
    public function getFullDescription()
    {
        return $this->fullDescription;
    }
 
    /**
     * Set fullDescription.
     *
     * @param fullDescription the value to set.
     */
    public function setFullDescription($fullDescription)
    {
        $this->fullDescription = $fullDescription;
        return $this;
    }
}
