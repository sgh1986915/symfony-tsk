<?php
namespace TSK\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use TSK\ScheduleBundle\Form\Type\RepeatEndsType;

class AttendanceFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\ScheduleBundle\Form\Model\Attendance',
            'compound' => true,
            'error_bubbling' => true,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Malaney, try using a roster object here instead of individual, school, class, schedule objects.  Then add a collection of notes to store any notes and finally use one date.


        // $builder->add('school', 'entity_hidden', array('my_class' => 'TSK\SchoolBundle\Entity\School'));
        // $builder->add('class', 'entity_hidden', array('my_class' => 'TSK\ClassBundle\Entity\Classes'));
        // $builder->add('schedule', 'entity_hidden', array('my_class' => 'TSK\ScheduleBundle\Entity\ScheduleEntity'));
        // $builder->add('student', 'entity_hidden', array('my_class' => 'TSK\StudentBundle\Entity\Student', 'label' => 'Select Student'));
        $builder->add('rosters', 'collection', array(
            'type' => 'entity_hidden',
            'allow_add' => true,
            'options' => array(
                'my_class' => 'TSK\ScheduleBundle\Entity\Roster'
            )
        ));
        // $builder->add('statuses', 'collection', array(
        //     'type' => 'choice',
        //     'allow_add' => true,
        //     'options' => array(
        //         'choices' => array('absent' => 'Absent', 'present' => 'Present'),
        //         'attr' => array('class' => 'input-medium'),
        //         'empty_value' => ' -- choose'
        //     )
        // ));
        $builder->add('attDate', 'date', array('label' => 'Attendance Date', 'widget' => 'single_text'));
        // $builder->add('status', 'choice', array(
        //     'choices' => array('absent' => 'Absent', 'present' => 'Present')
        // ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_schedule_attendance_type';
    }
}
