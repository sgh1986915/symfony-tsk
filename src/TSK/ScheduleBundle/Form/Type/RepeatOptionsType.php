<?php
namespace TSK\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use TSK\ScheduleBundle\Form\Type\RepeatEndsType;

class RepeatOptionsType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            // 'data_class' => 'TSK\ScheduleBundle\Entity\'
            'compound' => true
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('frequency', 'choice', array(
            'choices' => array(
                '3' => 'Daily',
                '2' => 'Weekly',
                '1' => 'Monthly',
                '0' => 'Yearly'
            ),
            'attr' => array('class' => 'input-medium')
        ));
        $builder->add('rinterval', 'choice', array(
            'choices' => array_combine(range(1,30), range(1,30)),
            'label' => 'Repeat every',
            'attr' => array('class' => 'input-medium'),
            // 'widget_controls_attr' => array('class' => 'pull-left')
        ));
        $builder->add('repeat_weekday', 'choice', array(
            'label' => 'Repeat on',
            'label_attr' => array('class' => 'inline'),
            'choices' => array(
                '6' => 'Su',
                '0' => 'Mo',
                '1' => 'Tu',
                '2' => 'We',
                '3' => 'Th',
                '4' => 'Fr',
                '5' => 'Sa'
            ),
            'expanded' => true,
            'multiple' => true
        ));
        $builder->add('repeat_monthday', 'choice', array(
            'label' => 'Repeat on',
            'label_attr' => array('class' => 'inline'),
            'choices' => array(
                'month_day' => 'day of the month',
                'week_day' => 'day of the week',
            ),
            'expanded' => true,
            'multiple' => false,
            'required' => false
        ));

        $builder->add('start', 'date', array(
            'widget' => 'single_text',
            'label' => 'Starts on',
            'data' => new \DateTime(),
            'attr' => array('class' => 'input-medium')
        ));
        $builder->add('ends', new RepeatEndsType(), array());
/*
        $builder->add('num_occurrences', 'text', array('required' => false));
        $builder->add('until_date', 'date', array(
            'widget' => 'single_text',
            'attr' => array('class' => 'input-medium')
        ));
*/
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_schedule_repeat_options';
    }
}
