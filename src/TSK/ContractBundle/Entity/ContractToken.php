<?php

namespace TSK\ContractBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * ContractToken
 *
 * @ORM\Table(name="tsk_contract_token")
 * @ORM\Entity(repositoryClass="TSK\ContractBundle\Entity\ContractTokenRepository")
 */
class ContractToken
{
    /**
     * @var integer
     *
     * @ORM\Column(name="contract_token_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false, onDelete="CASCADE")
     * @Assert\Type(type="TSK\ContractBundle\Entity\Contract")
     */
    private $contract;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer", nullable=true)
     */
    private $amount;

    /**
     * @var integer
     *
     * @ORM\Column(name="adjustment_amount", type="integer", nullable=true)
     */
    private $adjustmentAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expire_date", type="date", nullable=true)
     */
    private $expire_date;


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
     * Set amount
     *
     * @param integer $amount
     * @return ContractToken
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set expire_date
     *
     * @param \DateTime $expireDate
     * @return ContractToken
     */
    public function setExpireDate($expireDate)
    {
        $this->expire_date = $expireDate;
    
        return $this;
    }

    /**
     * Get expire_date
     *
     * @return \DateTime 
     */
    public function getExpireDate()
    {
        return $this->expire_date;
    }
 
    /**
     * Get contract.
     *
     * @return contract.
     */
    public function getContract()
    {
        return $this->contract;
    }
 
    /**
     * Set contract.
     *
     * @param contract the value to set.
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
        return $this;
    }
 
    /**
     * Get adjustmentAmount.
     *
     * @return adjustmentAmount.
     */
    public function getAdjustmentAmount()
    {
        return $this->adjustmentAmount;
    }
 
    /**
     * Set adjustmentAmount.
     *
     * @param adjustmentAmount the value to set.
     */
    public function setAdjustmentAmount($adjustmentAmount)
    {
        $this->adjustmentAmount = $adjustmentAmount;
        return $this;
    }
}
