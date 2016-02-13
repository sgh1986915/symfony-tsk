<?php
namespace TSK\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use TSK\PaymentBundle\Entity\PaymentMethod;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class PaymentMethodType extends AbstractType
{
    private $session;
    private $sessionKey;
    private $em;
    private $contactAdmin;
    public function __construct($session, $sessionKey, $em, $contactAdmin)
    {
        $this->em = $em;
        $this->session = $session;
        $this->sessionKey = $sessionKey;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\PaymentBundle\Model\PaymentAccount',
        ));
    }

    public function getName()
    {
        return 'tsk_payment_account';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $org = $this->session->get($this->sessionKey);
        $builder->add('paymentMethod', 'entity', array(
                'label' => 'Payment Method',
                'required' => true,
                'class' => 'TSKPaymentBundle:PaymentMethod',
                'property' => 'name',
                'empty_value' => 'Select a Payment Method',
                'query_builder' => function(EntityRepository $er) use ($org) {
                    return $er->createQueryBuilder('u')
                    ->where('u.organization=:org')
                    ->andWhere('u.isReceivable=1')
                    ->setParameter(':org', $org);
                }
        ));
        // $builder->add('ccNumber', 'text', array('label' => 'cc number'));
        // $builder->add('ccExpirationDate', 'date', array('label' => 'Date', 'widget' => 'single_text'));
        // $builder->add('cvvNumber', 'text', array('label' => 'cvv number'));
        // $builder->add('accountNumber', 'text', array('label' => 'account number'));
        // $builder->add('routingNumber', 'text', array('label' => 'routing number'));

        $factory = $builder->getFormFactory();
        $em = $this->em;
        $builder->addEventListener(
            FormEvents::PRE_BIND,
            function(FormEvent $event) use($factory, $em){
                $form = $event->getForm();
                $data = $event->getData();
                $paymentMethodID = (!empty($data['paymentMethod'])) ? $data['paymentMethod'] : '';
                if (!empty($paymentMethodID)) {
                    $paymentMethod = $em->getRepository('TSKPaymentBundle:PaymentMethod')->find($paymentMethodID);
                    if ($paymentMethod) {
                        if ($paymentMethod->getPaymentType() == 'CREDIT CARD') {
                            $form->add($factory->createNamed('creditCardNumber', 'text', NULL, array(
                                'label' => 'cc num', 
                                'required' => false, 
                            )));
                            $form->add($factory->createNamed('creditCardExpirationDate', 'text', NULL, array(
                                'label' => 'cc expiration date', 
                                'required' => false, 
                            )));

                            $form->add($factory->createNamed('cvvNumber', 'text', NULL, array(
                                'label' => 'cvv number', 
                                'required' => false, 
                            )));

                        } else if ($paymentMethod->getPaymentType() == 'ACH') {
                             $form->add($factory->createNamed('routingNumber', 'text', NULL, array(
                                'label' => 'routing number', 
                                'required' => false, 
                            )));
                             $form->add($factory->createNamed('accountNumber', 'text', NULL, array(
                                'label' => 'account number', 
                                'required' => false, 
                            )));
                        }
                    }
                }
            }
        );

      $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function(FormEvent $event) use($factory){
                $form = $event->getForm();
                $data = $event->getData();
                $contact = $data->getContact();

                if ($contact == null) {
                    return;
                } else {

                }
            }
        );
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
