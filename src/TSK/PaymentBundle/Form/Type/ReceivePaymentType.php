<?php
namespace TSK\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use TSK\PaymentBundle\Form\Type\OpenChargeType;
use TSK\PaymentBundle\Entity\Charge;
use TSK\PaymentBundle\Entity\ChargePayment;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class ReceivePaymentType extends AbstractType
{
    private $session;
    private $sessionKey;
    private $em;
    private $contactAdmin;
    public function __construct($session, $sessionKey, $em, $contactAdmin)
    {
        $this->session = $session;
        $this->sessionKey = $sessionKey;
        $this->em = $em;
        $this->contactAdmin = $contactAdmin;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\PaymentBundle\Form\Model\ReceivePayment',
            'show_date' => false,
            'is_credit' => false,
            'show_distribution_options' => false
        ));
    }

    public function getName()
    {
        return 'tsk_receive_payment';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $org = $this->session->get($this->sessionKey);
        $builder->add('contact', 'tsk_contact_list', array(
            'label' => false,
            'show_add' => false, 
            'admin' => $this->contactAdmin,
            'list_label' => 'Select Contact') 
        );
        if ($options['show_date']) {
            $builder->add('paymentDate', 'date', array('label' => 'Date', 'widget' => 'single_text', 'data' => new \DateTime()));
        }
        $builder->add('paymentAmount', 'text', array('label' => 'Amount'));
        if ($options['show_distribution_options']) {
            $builder->add('paymentDistributionStrategy', 'choice', array('mapped' => true, 'choices' => array('eagerly' => 'Apply Eagerly', 'evenly' => 'Apply Evenly'), 'label' => 'Distribution Strategy', 'multiple' => false, 'expanded' => true));
        }
        $builder->add('paymentType', 'entity_hidden', array(
                'label' => 'Payment Type',
                'required' => true,
                'my_class' => 'TSKPaymentBundle:PaymentType',
                // 'property' => 'name',
                // 'empty_value' => 'Select a Payment Type',
                // 'query_builder' => function(EntityRepository $er) use ($org) {
                //     return $er->createQueryBuilder('u')
                //     ->where('u.organization=:org')
                //     ->setParameter(':org', $org);
                // }
        ));

        if ($options['is_credit']) {
            $builder->add('paymentMethod', 'entity_hidden', array(
                    'label' => false,
                    'required' => true,
                    'my_class' => 'TSKPaymentBundle:PaymentMethod'
            ));

        } else {
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
        }
        // $builder->add('paymentType', 'entity', array(
        //         'label' => 'Payment Type',
        //         'required' => true,
        //         'class' => 'TSKPaymentBundle:PaymentType',
        //         'property' => 'name',
        //         'empty_value' => 'Select a Payment Type',
        //         'query_builder' => function(EntityRepository $er) use ($org) {
        //             return $er->createQueryBuilder('u')
        //             ->where('u.organization=:org')
        //             ->setParameter(':org', $org);
        //         }
        // ));

        if (!$options['is_credit']) {
            $builder->add('refNumber', 'text', array('label' => 'Ref. Number', 'required' => false));
        }
        $builder->add('memo', 'text', array('attr' => array('style' => 'width: 600px;', 'required' => false)));

        $factory = $builder->getFormFactory();
        $em = $this->em;
        $builder->addEventListener(
            FormEvents::PRE_BIND,
            function(FormEvent $event) use($factory, $em){
                $form = $event->getForm();

                $data = $event->getData();
                $contactID = $data['contact'];

                if ($contactID) {
                    $form->add($factory->createNamed('charges', 'tsk_charge_payment_type', NULL,  array(
                        'required' => false, 
                        'label' => 'Payment Amount', 
                        'class' => 'TSKPaymentBundle:Charge', 
                        'property' => 'amount', 
                        'multiple' => true, 
                        'attr' => array('class' => 'chg_cbx input-small'),
                        'widget_controls_attr' => array('class' => 'chg_cbx'),
                        'widget_control_group_attr' => array('class' => 'chg_cbx'),
                        'render_optional_text' => false,
                        'data_class' => NULL, 
                        'expanded' => true,
                        'query_builder' => function(EntityRepository $er) use ($contactID) {
                            return  $er->createQueryBuilder('c')
                                        ->leftJoin('c.contracts', 'ct')
                                        ->join('ct.students', 's')
                                        ->where('s.contact=:contact')
                                        ->setParameter('contact', $contactID);
                        }

                    )));
                }

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
                    $form->add($factory->createNamed('charges', 'tsk_charge_payment_type', NULL,  array(
                        'required' => true, 
                        'label' => 'Payment Amount', 
                        'class' => 'TSKPaymentBundle:Charge', 
                        'property' => 'amount', 
                        'multiple' => true, 
                        'render_optional_text' => false,
                        'data_class' => NULL, 
                        'attr' => array('class' => 'chg_cbx'),
                        'widget_controls_attr' => array('class' => 'chg_cbx'),
                        'widget_control_group_attr' => array('class' => 'chg_cbx'),
                        'expanded' => true,
                        'query_builder' => function(EntityRepository $er) use ($contact) {
                            return  $er->createQueryBuilder('c')
                                        ->leftJoin('c.contracts', 'ct')
                                        ->leftJoin('ct.students', 's')
                                        ->where('s.contact=:contact')
                                        ->setParameter('contact', $contact);
                        }
                    )));

                }
                

                // ... proceed with customizing the form based on available positions
            }
        );
/*
        $query = $this->em->createQuery('SELECT s, ct, c FROM TSK\StudentBundle\Entity\Student s LEFT JOIN s.contracts ct LEFT JOIN ct.charges c LEFT JOIN c.chargePayments cp WHERE s.contact=46'); 
        // $query->setParameter(1, $contact);
        $student = $query->getSingleResult();

        foreach ($student->getContracts() as $contract) {
            foreach ($contract->getCharges() as $charge) {
                if ($charge->getOpenAmount()) {
                    $cp = new ChargePayment();
                    $cp->setCharge($charge);
                    $charges[] = $cp;
                }
            }
        }

        $c = new Charge();
        $c->setAmount(100);
        $c->setDueDate(new \DateTime('2013-04-29'));
        $cp = new ChargePayment();
        $cp->setCharge($c);
               
        $builder->add('charges', 'collection', array(
                'type' => new OpenChargeType(),
                'data' => $charges,
                'allow_add' => true,
                'prototype' => true,
                'options' => array(
                    'widget_checkbox_label' => 'widget',
                    'required' => false,
                    'render_optional_text' => false,
                    'attr' => array('class' => 'foo')
                )
            )
        );
*/
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
