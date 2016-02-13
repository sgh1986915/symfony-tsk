<?php

namespace TSK\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Corporation
 *
 * @ORM\Table(name="tsk_corporation")
 * @ORM\Entity
 */
class Corporation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="corporation_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_id", type="string", length=20, nullable=false)
     */
    private $taxId;

    /**
     * @var string
     *
     * @ORM\Column(name="account_num", type="string", length=50, nullable=true)
     */
    private $accountNum;

    /**
     * @var string
     *
     * @ORM\Column(name="routing_num", type="string", length=50, nullable=true)
     */
    private $routingNum;

    /**
     * @var string
     *
     * @ORM\Column(name="legal_name", type="string", length=50, nullable=false)
     */
    private $legalName;

    /**
     * @var string
     *
     * @ORM\Column(name="abbr_legal_name", type="string", length=50, nullable=true)
     */
    private $abbrLegalName;

    /**
     * @var string
     *
     * @ORM\Column(name="dba", type="string", length=50, nullable=true)
     */
    private $dba;


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
     * Set taxId
     *
     * @param string $taxId
     * @return Corporation
     */
    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;
    
        return $this;
    }

    /**
     * Get taxId
     *
     * @return string 
     */
    public function getTaxId()
    {
        return $this->taxId;
    }

    /**
     * Set account_num
     *
     * @param string $accountNum
     * @return Corporation
     */
    public function setAccountNum($accountNum)
    {
        $this->accountNum = $accountNum;
    
        return $this;
    }

    /**
     * Get account_num
     *
     * @return string 
     */
    public function getAccountNum()
    {
        return $this->account_num;
    }

    /**
     * Set routingNum
     *
     * @param string $routingNum
     * @return Corporation
     */
    public function setRoutingNum($routingNum)
    {
        $this->routingNum = $routingNum;
    
        return $this;
    }

    /**
     * Get routingNum
     *
     * @return string 
     */
    public function getRoutingNum()
    {
        return $this->routingNum;
    }

    /**
     * Set legalName
     *
     * @param string $legalName
     * @return Corporation
     */
    public function setLegalName($legalName)
    {
        $this->legalName = $legalName;
    
        return $this;
    }

    /**
     * Get legalName
     *
     * @return string 
     */
    public function getLegalName()
    {
        return $this->legalName;
    }

    /**
     * Set dba
     *
     * @param string $dba
     * @return Corporation
     */
    public function setDba($dba)
    {
        $this->dba = $dba;
    
        return $this;
    }

    /**
     * Get dba
     *
     * @return string 
     */
    public function getDba()
    {
        return $this->dba;
    }

    public function __toString()
    {
        return $this->getLegalName();
    }
 
    /**
     * Get abbrLegalName.
     *
     * @return abbrLegalName.
     */
    public function getAbbrLegalName()
    {
        return $this->abbrLegalName;
    }
 
    /**
     * Set abbrLegalName.
     *
     * @param abbrLegalName the value to set.
     */
    public function setAbbrLegalName($abbrLegalName)
    {
        $this->abbrLegalName = $abbrLegalName;
        return $this;
    }
}
