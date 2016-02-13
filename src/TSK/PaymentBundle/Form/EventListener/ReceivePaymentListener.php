<?php
namespace TSK\PaymentBundle\Form\EventListener;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;

class ReceivePaymentListener implements EventSubscriberInterface
{
    private $factory;
    private $em;

    public function __construct(FormFactoryInterface $factory, EntityManager $em)
    {
        $this->factory = $factory;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_BIND => 'preBind',
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    public function preSetData(FormEvent $event)
    {
        $receivePayment = $event->getData()->getContact();
        
        if (null === $receivePayment) {
            return;
        }
        $form = $event->getForm();
        $charges = $this->em->getRepository('TSKUserBundle:Contact')->getOpenChargesForContact($receivePayment->getContact());
        $this->customizeForm($form, $charges);
    }

    public function preBind(FormEvent $event)
    {
        $data = $event->getData();
        $contact = $data['contact'];
        $charges = $this->em->getRepository('TSKUserBundle:Contact')->getOpenChargesForContact($receivePayment->getContact());

        $form = $event->getForm();

        $this->customizeForm($form, $charges);
    }

    protected function customizeForm($form, $charges)
    {
        // add charges to form
    }
}
