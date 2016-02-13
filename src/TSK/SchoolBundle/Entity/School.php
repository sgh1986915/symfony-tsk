<?php

namespace TSK\SchoolBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * School
 *
 * @ORM\Table(name="tsk_school", uniqueConstraints={@ORM\UniqueConstraint(name="school_legacy_id", columns={"legacy_id"})})
 * @ORM\Entity(repositoryClass="TSK\SchoolBundle\Entity\SchoolRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class School
{
    /**
     * @var integer
     *
     * @ORM\Column(name="school_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     *
     * @ORM\OneToOne(targetEntity="TSK\UserBundle\Entity\Contact", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="fk_contact_id", referencedColumnName="contact_id", nullable=false, onDelete="CASCADE")
     * @Assert\Type(type="TSK\UserBundle\Entity\Contact")
     */
    protected $contact;

    /**
     * @ORM\ManyToMany(targetEntity="TSK\UserBundle\Entity\Contact", mappedBy="schools")
     */
    protected $schoolContacts;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="legacy_id", type="string", length=255, nullable=true)
     */
    protected $legacyId;

    /**
     * @var string
     *
     * @ORM\Column(name="deferral_rate", type="float", nullable=true)
     */
    protected $deferralRate;

    /**
     * @var string
     *
     * @ORM\Column(name="distribution_strategy", type="string", nullable=true)
     */
    protected $distributionStrategy;

    /**
     * @var float
     *
     * @ORM\Column(name="payment_max", type="float", nullable=true)
     */
    protected $paymentMax;

    /**
     * @var integer
     *
     * @ORM\Column(name="late_grace_days", type="integer", nullable=true)
     */
    protected $lateGraceDays;

    /**
     * @var float
     *
     * @ORM\Column(name="late_payment_charge", type="float", nullable=true)
     */
    protected $latePaymentCharge;

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
     * @return School
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

    public function setTaxId($taxId)
    {
        $this->taxId = $taxId;
        return $this;
    }
    
    public function getTaxId()
    {
        return $this->taxId;
    }

    public function setLegacyId($legacyId)
    {
        $this->legacyId = $legacyId;
        return $this;
    }
    
    public function getLegacyId()
    {
        return $this->legacyId;
    }

    public function setCheckingAcctNum($checkingAcctNum)
    {
        $this->checkingAcctNum = $checkingAcctNum;
        return $this;
    }
    
    public function getCheckingAcctNum()
    {
        return $this->checkingAcctNum;
    }

    public function setLegalName($legalName)
    {
        $this->legalName = $legalName;
        return $this;
    }
    
    public function getLegalName()
    {
        return $this->legalName;
    }

    public function setAlias($alias)
    {
        $this->alias = $alias;
        return $this;
    }
    
    public function getAlias()
    {
        return $this->alias;
    }

    public function setRoutingNum($routingNum)
    {
        $this->routingNum = $routingNum;
        return $this;
    }
    
    public function getRoutingNum()
    {
        return $this->routingNum;
    }

    public function getContact()
    {
        return $this->contact;
    }

    public function setContact(\TSK\UserBundle\Entity\Contact $contact)
    {
        $this->contact = $contact;
        return $this;
    }

    public function __toString()
    {
        if ($this->getName()) {
            return $this->getName();
        } else {
            return '<new school>';
        }
    }

 
    /**
     * Get deferralRate.
     *
     * @return deferralRate.
     */
    public function getDeferralRate()
    {
        return $this->deferralRate;
    }
 
    /**
     * Set deferralRate.
     *
     * @param deferralRate the value to set.
     */
    public function setDeferralRate($deferralRate)
    {
        $this->deferralRate = $deferralRate;
        return $this;
    }
 
    /**
     * Get distributionStrategy.
     *
     * @return distributionStrategy.
     */
    public function getDistributionStrategy()
    {
        return $this->distributionStrategy;
    }
 
    /**
     * Set distributionStrategy.
     *
     * @param distributionStrategy the value to set.
     */
    public function setDistributionStrategy($distributionStrategy)
    {
        $this->distributionStrategy = $distributionStrategy;
        return $this;
    }
 
 /**
  * Get paymentMax.
  *
  * @return paymentMax.
  */
 function getPaymentMax()
 {
     return $this->paymentMax;
 }
 
 /**
  * Set paymentMax.
  *
  * @param paymentMax the value to set.
  */
 function setPaymentMax($paymentMax)
 {
     $this->paymentMax = $paymentMax;
 }
 
    /**
     * Get latePaymentCharge.
     *
     * @return latePaymentCharge.
     */
    public function getLatePaymentCharge()
    {
        return $this->latePaymentCharge;
    }
 
    /**
     * Set latePaymentCharge.
     *
     * @param latePaymentCharge the value to set.
     */
    public function setLatePaymentCharge($latePaymentCharge)
    {
        $this->latePaymentCharge = $latePaymentCharge;
        return $this;
    }
 
    /**
     * Get lateGraceDays.
     *
     * @return lateGraceDays.
     */
    public function getLateGraceDays()
    {
        return $this->lateGraceDays;
    }
 
    /**
     * Set lateGraceDays.
     *
     * @param lateGraceDays the value to set.
     */
    public function setLateGraceDays($lateGraceDays)
    {
        $this->lateGraceDays = $lateGraceDays;
        return $this;
    }

    /**
     * prePersist 
     * 
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $t = microtime(true);
        $micro = sprintf("%06d",($t - floor($t)) * 1000000);
        $now = new \DateTime(date('Y-m-d H:i:s.' . $micro, $t));
        if (!$this->getLegacyId()) {
            $this->setLegacyId($now->format('Ymdhis.u'));
        }
    }
}
