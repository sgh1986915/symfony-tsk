<?php

namespace TSK\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Deferral
 *
 * @ORM\Table(name="tsk_deferral")
 * @ORM\Entity
 */
class Deferral
{
    /**
     * @var integer
     *
     * @ORM\Column(name="deferral_id", type="integer")
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
     * @ORM\Column(name="payment_term", type="string", length=100)
     */
    private $paymentTerm;

    /**
     * @var smallint
     *
     * @ORM\Column(name="program_length", type="smallint")
     */
    private $programLength;

    /**
     * @var float
     *
     * @ORM\Column(name="deferral_rate", type="float")
     */
    private $deferralRate;

    /**
     * @var smallint
     *
     * @ORM\Column(name="deferral_duration", type="smallint")
     */
    private $deferralDuration;


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
     * Set payment_term
     *
     * @param string $paymentTerm
     * @return Deferral
     */
    public function setPaymentTerm($paymentTerm)
    {
        $this->paymentTerm = $paymentTerm;
    
        return $this;
    }

    /**
     * Get payment_term
     *
     * @return string 
     */
    public function getPaymentTerm()
    {
        return $this->paymentTerm;
    }

    /**
     * Set programLength
     *
     * @param integer $programLength
     * @return Deferral
     */
    public function setProgramLength($programLength)
    {
        $this->programLength = $programLength;
    
        return $this;
    }

    /**
     * Get programLength
     *
     * @return integer 
     */
    public function getProgramLength()
    {
        return $this->programLength;
    }

    /**
     * Set deferralRate
     *
     * @param float $deferralRate
     * @return Deferral
     */
    public function setDefermentRate($deferralRate)
    {
        $this->deferralRate = $deferralRate;
    
        return $this;
    }

    /**
     * Get deferralRate
     *
     * @return float 
     */
    public function getDefermentRate()
    {
        return $this->deferralRate;
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
        return $this->getProgramLength() .'_'. $this->getPaymentTerm();
    }

 
    /**
     * Get deferralDuration.
     *
     * @return deferralDuration.
     */
    public function getDeferralDuration()
    {
        return $this->deferralDuration;
    }
 
    /**
     * Set deferralDuration.
     *
     * @param deferralDuration the value to set.
     */
    public function setDeferralDuration($deferralDuration)
    {
        $this->deferralDuration = $deferralDuration;
        return $this;
    }
}
