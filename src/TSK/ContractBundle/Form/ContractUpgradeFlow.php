<?php
namespace TSK\ContractBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;



class ContractUpgradeFlow extends FormFlow implements EventSubscriberInterface {

    protected $maxSteps = 6;
    protected $allowDynamicStepNavigation = true;

    public function __construct()
    {
        $this->addSkipStep(2);
    }

    protected function loadStepDescriptions() 
    {
        return array(
            'Select Discount Type',
            'Select Training Family Members',
            'Select Program',
            'Select Payment Plan',
            'Customize Payments',
            'Confirm'
        );
    }

    public function getFormOptions($formData, $step, array $options = array()) 
    {
        $options = parent::getFormOptions($formData, $step, $options);

        if ($step > 0) {
            $options['contract'] = $formData->getContract();
            $options['student'] = $formData->getContract()->getStudents()->first();
            $options['school'] = $formData->getContract()->getSchool();
        }
        if ($step > 1) {
            $options['program'] = $formData->getProgram();
            // $options['programExcludes'] = $formData->getProgramExcludes();
            $options['programDiscountTypeFilters'] = $formData->getProgramDiscountTypeFilters();
        }
        if ($step > 4) {
            $options['programPaymentPlan'] = $formData->getProgramPaymentPlan();
            // ld($options['programPaymentPlan']);
            // $paymentPlanJson = $formData->getProgramPaymentPlan()->getPaymentsData();
            // $obj = json_decode($paymentPlanJson['paymentsData']);
            // $obj->payments = array_fill(0, count($obj->payments), 0);
            // $obj->principal = $obj->principal - $formData->getContract()->getCreditDue();
            // $options['programPaymentPlanData']
        }

        return $options;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher) 
    {
        parent::setEventDispatcher($dispatcher);
        $dispatcher->addSubscriber($this);
    }

    public static function getSubscribedEvents() 
    {
        return array(
            FormFlowEvents::PRE_BIND => 'onPreBind',
            FormFlowEvents::POST_BIND_REQUEST => 'onPostBindRequest',
            FormFlowEvents::POST_BIND_SAVED_DATA => 'onPostBindSavedData',
            FormFlowEvents::POST_VALIDATE => 'onPostValidate',
        );
    }

    public function onPreBind(PreBindEvent $event) 
    {
        if ($this->isFamilyDiscount()) {
            $this->removeSkipStep(2);
        } else {
        }
    }

    public function onPostBindRequest(PostBindRequestEvent $event) 
    {
        // ...
        $flow = $event->getFlow();
        // If discount level is 'individual' then we can skip
        // the training family members step
        if ($event->getFormData()->getDiscountLevel() == 'individual') {
            $this->addSkipStep(2);
        }

    }

    public function onPostBindSavedData(PostBindSavedDataEvent $event) 
    {
        // ...
    }

    public function getName()
    {
    }

    public function onPostValidate(PostValidateEvent $event) 
    {
        $data = $event->getFormData();
        if ($this->currentStep >= 1) {
            if ($data->getDiscountLevel() == 'individual') {
                $this->removeTempIsFamilyDiscount();
                $this->addSkipStep(2);
            } else {
                $this->setTempIsFamilyDiscount();
                $this->removeSkipStep(2);
            }
        }

    }

    public function isFamilyDiscount() {
        return $this->storage->get($this->getTempIsFamilyDiscountSessionKey(), false);
    }

    public function getTempIsFamilyDiscountSessionKey() {
        return $this->id . '_isFamilyDiscount';
    }

    public function removeTempIsFamilyDiscount()
    {
        $this->storage->remove($this->getTempIsFamilyDiscountSessionKey());
    }

    public function setTempIsFamilyDiscount()
    {
        $this->storage->set($this->getTempIsFamilyDiscountSessionKey(), true);
    }

}
