<?php
namespace TSK\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class RepeatEndsType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            // 'data_class' => 'TSK\ScheduleBundle\Entity\'
            'compound' => true,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('ends_type', 'choice', array(
            'label' => false,
            'choices' => array(
                'never' => 'Never',
                'after' => 'After',
                'until' => 'Until'
            ),
            'expanded' => true,
            'multiple' => false,
            'data' => 'never' // pre-selected option
        ));
        $builder->add('num_occurrences', 'text', array(
            'attr' => array('class' => 'input-mini'),
            'required' => false
        ));
        $builder->add('until_date', 'date', array(
            'widget' => 'single_text',
            'required' => false
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_schedule_repeat_ends';
    }
}
