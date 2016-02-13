<?php

namespace TSK\BilleeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BilleePaymentMethodContract
 *
 * @ORM\Table(name="tsk_bpm_contract")
 * @ORM\Entity
 */
class BilleePaymentMethodContract
{
    /**
     * @var integer
     *
     * @ORM\Column(name="bpm_contract_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\BilleeBundle\Entity\BilleePaymentMethod", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_bpm_id", referencedColumnName="billee_payment_method_id", nullable=false, unique=false)
     */
    private $billeePaymentMethod;

    /**
     *
     * @ORM\ManyToOne(targetEntity="TSK\ContractBundle\Entity\Contract", cascade={"persist"})
     * @ORM\JoinColumn(name="fk_contract_id", referencedColumnName="contract_id", nullable=false, unique=false)
     */
    private $contract;

    /**
     * @var float
     *
     * @ORM\Column(name="portion", type="smallint")
     */
    private $portion;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="string", length=100, nullable=true)
     */
    private $notes;


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
     * Set portion
     *
     * @param float $portion
     * @return BilleePaymentMethodContract
     */
    public function setPortion($portion)
    {
        $this->portion = $portion;
    
        return $this;
    }

    /**
     * Get portion
     *
     * @return float 
     */
    public function getPortion()
    {
        return $this->portion;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return BilleePaymentMethodContract
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }
 
    /**
     * Get billeePaymentMethod.
     *
     * @return billeePaymentMethod.
     */
    public function getBilleePaymentMethod()
    {
        return $this->billeePaymentMethod;
    }
 
    /**
     * Set billeePaymentMethod.
     *
     * @param billeePaymentMethod the value to set.
     */
    public function setBilleePaymentMethod($billeePaymentMethod)
    {
        $this->billeePaymentMethod = $billeePaymentMethod;
        return $this;
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
}
