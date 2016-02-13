<?php
namespace TSK\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ChargePaymentType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\PaymentBundle\Entity\ChargePayment',
            'compound' => true,
            'required' => false,
            'dataType' => 'entity'
        ));
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->setAttribute('dataType', $options['dataType']);
/*

        $builder->add('charge', 'checkbox', array(
                'required' => false,
                'mapped' => true,
                'data_class' => 'TSK\PaymentBundle\Entity\Charge',
                'property' => 'id',
                'render_optional_text' => false,
                'widget_checkbox_label' => 'widget',
        ));
        $builder->add('payment', 'text', array(
                // 'data_class' => 'TSK\PaymentBundle\Entity\Payment'
            )
        );
*/
    }

  /**
     * Returns the allowed option values for each option (if any).
     *
     * @param array $options
     *
     * @return array The allowed option values
     */
    public function getAllowedOptionValues(array $options)
    {
        return array('required' => array(false));
    }

    public function getParent()
    {
        return 'entity';
    }

    public function getName()
    {
        return 'tsk_charge_payment_type';
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }
}
