<?php
namespace TSK\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentCalculatorType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'compound' => true,
                'hide_data' => true,
                'hide_button' => false,
                'hide_summary' => false
        ));
    }

/*
    public function getDefaultOptions(array $options)
    {
        return array(
                'compound' => true,
                'hide_data' => true,
                'hide_button' => false,
                'hide_summary' => false
        );
    }
*/

    public function getName()
    {
        return 'tsk_payment_calculator';
    }

    public function getParent()
    {
        return 'text';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $textAreaDisplay = ($options['hide_data']) ? 'none' : 'block';
        $builder->add('paymentsData', 'textarea', array('attr' => array('style' => 'display: ' . $textAreaDisplay)));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['hide_data'] = $options['hide_data'];
        $view->vars['hide_button'] = $options['hide_button'];
    }
}
