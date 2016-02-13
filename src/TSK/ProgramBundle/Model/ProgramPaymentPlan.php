<?php
namespace TSK\ProgramBundle\Model;

class ProgramPaymentPlan
{
    protected $id;
    protected $name;
    protected $price;
    protected $minPayments;
    protected $deferralRate;
    protected $deferralDurationMonths;
    protected $deferralDistributionStrategy;
    protected $paymentsData;
    protected $isActive;
 
    /**
     * Get name.
     *
     * @return name.
     */
    public function getName()
    {
        return $this->name;
    }
 
    /**
     * Set name.
     *
     * @param name the value to set.
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
 
    /**
     * Get price.
     *
     * @return price.
     */
    public function getPrice()
    {
        return $this->price;
    }
 
    /**
     * Set price.
     *
     * @param price the value to set.
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }
 
    /**
     * Get minPayments.
     *
     * @return minPayments.
     */
    public function getMinPayments()
    {
        return $this->minPayments;
    }
 
    /**
     * Set minPayments.
     *
     * @param minPayments the value to set.
     */
    public function setMinPayments($minPayments)
    {
        $this->minPayments = $minPayments;
        return $this;
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
     * Get deferralDurationMonths.
     *
     * @return deferralDurationMonths.
     */
    public function getDeferralDurationMonths()
    {
        return $this->deferralDurationMonths;
    }
 
    /**
     * Set deferralDurationMonths.
     *
     * @param deferralDurationMonths the value to set.
     */
    public function setDeferralDurationMonths($deferralDurationMonths)
    {
        $this->deferralDurationMonths = $deferralDurationMonths;
        return $this;
    }
 
    /**
     * Get deferralDistributionStrategy.
     *
     * @return deferralDistributionStrategy.
     */
    public function getDeferralDistributionStrategy()
    {
        return $this->deferralDistributionStrategy;
    }
 
    /**
     * Set deferralDistributionStrategy.
     *
     * @param deferralDistributionStrategy the value to set.
     */
    public function setDeferralDistributionStrategy($deferralDistributionStrategy)
    {
        $this->deferralDistributionStrategy = $deferralDistributionStrategy;
        return $this;
    }
 
    /**
     * Get paymentsData.
     *
     * @return paymentsData.
     */
    public function getPaymentsData()
    {
        return $this->paymentsData;
    }
 
    /**
     * Set paymentsData.
     *
     * @param paymentsData the value to set.
     */
    public function setPaymentsData($paymentsData)
    {
        $this->paymentsData = $paymentsData;
        return $this;
    }
 
    /**
     * Get isActive.
     *
     * @return isActive.
     */
    public function getIsActive()
    {
        return $this->isActive;
    }
 
    /**
     * Set isActive.
     *
     * @param isActive the value to set.
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
        return $this;
    }
 
    /**
     * Get id.
     *
     * @return id.
     */
    public function getId()
    {
        return $this->id;
    }
 
    /**
     * Set id.
     *
     * @param id the value to set.
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }
}
