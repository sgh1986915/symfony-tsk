<?php
namespace TSK\ContractBundle\Form\Model;

class ContractModifier
{
    protected $contract;
    protected $programPaymentPlan;
    protected $paymentPlanCustomizedPayments;
 
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
     * Get programPaymentPlan.
     *
     * @return programPaymentPlan.
     */
    public function getProgramPaymentPlan()
    {
        return $this->programPaymentPlan;
    }
 
    /**
     * Set programPaymentPlan.
     *
     * @param programPaymentPlan the value to set.
     */
    public function setProgramPaymentPlan($programPaymentPlan)
    {
        $this->programPaymentPlan = $programPaymentPlan;
        return $this;
    }
 
    /**
     * Get paymentPlanCustomizedPayments.
     *
     * @return paymentPlanCustomizedPayments.
     */
    public function getPaymentPlanCustomizedPayments()
    {
        return $this->paymentPlanCustomizedPayments;
    }
 
    /**
     * Set paymentPlanCustomizedPayments.
     *
     * @param paymentPlanCustomizedPayments the value to set.
     */
    public function setPaymentPlanCustomizedPayments($paymentPlanCustomizedPayments)
    {
        $this->paymentPlanCustomizedPayments = $paymentPlanCustomizedPayments;
        return $this;
    }
}
