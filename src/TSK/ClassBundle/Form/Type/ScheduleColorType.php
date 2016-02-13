<?php
namespace TSK\ClassBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ScheduleColorType extends AbstractType
{
    public function getName()
    {
        return 'tsk_class_schedule_color_type';
    }
    
    public function getParent()
    {
        return 'text';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'attr' => array('class' => 'colorpicker'),
            'required' => false
        ));
    }
}
