<?php
namespace TSK\StudentBundle\Form;

use Craue\FormFlowBundle\Form\FormFlow;
use Craue\FormFlowBundle\Event\PostBindRequestEvent;
use Craue\FormFlowBundle\Event\PostBindSavedDataEvent;
use Craue\FormFlowBundle\Event\PostValidateEvent;
use Craue\FormFlowBundle\Event\PreBindEvent;
use Craue\FormFlowBundle\Form\FormFlowEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;



class RegisterStudentFlow extends FormFlow implements EventSubscriberInterface {

    protected $maxSteps = 9;
    protected $allowDynamicStepNavigation = true;

    protected function loadStepDescriptions() 
    {
        return array(
            'Collect Student Info',
            'Collect Emergency Contact Info',
            'Discount Level',
            'Training Family Members',
            'Select Program',
            'Configure Payments',
            'Collect Billee Info',
            'Enter Recurring Payment Method Details',
            'Confirm'
        );
    }

    public function getFormOptions($formData, $step, array $options = array()) 
    {
        $options = parent::getFormOptions($formData, $step, $options);

        if ($step > 0) {
            $options['school'] = $formData->getSchool();
        }
        if ($step > 1) {
            $options['program'] = $formData->getProgram();
        }
        if ($step > 2) {
            $options['programExcludes'] = $formData->getProgramExcludes();
        }
        if ($step > 3) {
            // $options['studentDateOfBirth'] = $formData->getStudentDateOfBirth();
            $options['student'] = $formData->getStudentContact();
            $options['programDiscountTypeFilters'] = $formData->getProgramDiscountTypeFilters();
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

    /**
     * onPreBind 
     * In order to handle the skip step properly, we need to set
     * a session var that can be read by the onPreBind method.  Here
     * we read the session var that may have been set down in 
     * onPostValidate
     * 
     * @param PreBindEvent $event 
     * @access public
     * @return void
     */
    public function onPreBind(PreBindEvent $event) 
    {
        if (!$this->isFamilyDiscount()) {
            $this->addSkipStep(4);
        } else {
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

    public function onPostBindRequest(PostBindRequestEvent $event) 
    {
        $flow = $event->getFlow();
        // If discount level is 'individual' then we can skip
        // the training family members step
        if ($event->getFormData()->getDiscountLevel() == 'individual') {
            $this->addSkipStep(4);
        }
    }

    public function onPostBindSavedData(PostBindSavedDataEvent $event) 
    {
        // ...
    }

    public function getName()
    {
    }

    /**
     * onPostValidate 
     * In this method we conditionally save a session var
     * depending on whether the registration is for a familyDiscount
     * This session var is used in onPreBind ...
     * 
     * @param PostValidateEvent $event 
     * @access public
     * @return void
     */
    public function onPostValidate(PostValidateEvent $event) 
    {
        $data = $event->getFormData();
        if ($this->currentStep >= 1) {
            if ($data->getDiscountLevel() == 'individual') {
                $this->removeTempIsFamilyDiscount();
                $this->addSkipStep(4);
            } else {
                $this->setTempIsFamilyDiscount();
                $this->removeSkipStep(4);
            }
        }
    }
}
