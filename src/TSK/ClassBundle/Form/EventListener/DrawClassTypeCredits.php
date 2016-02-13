<?php
namespace TSK\ClassBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use TSK\ClassBundle\Entity\ClassTypeCredit;

class DrawClassTypeCredits implements EventSubscriberInterface
{
    private $factory;
    private $em;
    private $org_id;

    public function __construct(FormFactoryInterface $factory, $em, $org_id=0)
    {
        $this->factory = $factory;
        $this->em = $em;
        $this->org_id = $org_id;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that you want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. You're only concerned with when
        // setData is called with an actual Entity object in it (whether new
        // or fetched with Doctrine). This if statement lets you skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        // Defaults
        $defaults = array();
        $classTypes = $this->em->createQuery('SELECT u from TSK\ClassBundle\Entity\ClassType u WHERE u.organization=:org')->setParameter('org', $this->org_id)->getResult();
        
        if (count($classTypes)) {
            foreach ($classTypes as $classType) {
                $ctc = new ClassTypeCredit();
                $ctc->setClassType($classType);
                $ctc->setClass($data);
                $ctc->setValue(0);
                $defaults[$classType->getName()] = $ctc;
            }
        }

        $results = array();
        foreach ($data->getClassTypeCredits() as $idx => $ctc) {
            $results[$ctc->getClassType()->getName()] = $ctc;
        }
        $results = array_merge($defaults, $results);

        $payload = 
            array(
                'type' => 'tsk_class_types',
                'label' => ' ',
                'required' => false,
                'data' => $results
                );
        
        $form->add($this->factory->createNamed('classTypeCredits', 'collection', NULL, $payload));
    }
}
