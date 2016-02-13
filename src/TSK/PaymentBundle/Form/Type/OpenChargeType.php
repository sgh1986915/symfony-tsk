<?php
namespace TSK\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class OpenChargeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\PaymentBundle\Entity\ChargePayment',
            'compound' => true
        ));
    }

    public function getName()
    {
        return 'tsk_open_charge_type';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('use_charge', 'checkbox', array(
                'required' => false,
                'mapped' => false,
                'render_optional_text' => false,
                'widget_checkbox_label' => 'widget',
        ));
        $builder->add('charge', 'text', array(
            'data_class' => 'TSK\PaymentBundle\Entity\Charge',
        ));

        $builder->add('payment', 'text', array(
                'data_class' => 'TSK\PaymentBundle\Entity\Payment'
            )
        );
        // $builder->add('id');
        // $builder->add('dueDate', 'date', array(
        //     'widget' => 'single_text'
        // ));

        // $builder->add('amount', 'text', array(
        //     'render_optional_text' => false
        // ));
        // $builder->add('openAmount');
        // $builder->add('paidAmount', 'text', array(
        //     'mapped' => false
        // ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
