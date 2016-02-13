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



class ContractModifierFlow extends FormFlow implements EventSubscriberInterface {

    protected $maxSteps = 4;
    protected $allowDynamicStepNavigation = true;

    protected function loadStepDescriptions() 
    {
        return array(
            'Review Contract',
            'Program Type',
            'Select Program',
            'Confirm'
        );
    }

    public function getFormOptions($formData, $step, array $options = array()) 
    {
        $options = parent::getFormOptions($formData, $step, $options);

        if ($step > 0) {
            $options['contract'] = $formData->getContract();
        }
        if ($step > 1) {
            // $options['program'] = $formData->getProgram();
        }
        if ($step > 3) {
            // $options['studentDateOfBirth'] = $formData->getStudentDateOfBirth();
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
        // ...
    }

    public function onPostBindRequest(PostBindRequestEvent $event) 
    {
        // ...
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
    }
}
