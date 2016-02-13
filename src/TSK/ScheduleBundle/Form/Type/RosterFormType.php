<?php
namespace TSK\ScheduleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use TSK\ScheduleBundle\Form\Type\RepeatEndsType;

class RosterFormType extends AbstractType
{
    private $contactAdmin;
    private $contactAdminName;
    public function __construct($contactAdmin, $contactAdminName)
    {
        $this->contactAdmin = $contactAdmin;
        $this->contactAdminName = $contactAdminName;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\ScheduleBundle\Entity\Roster',
            'compound' => true,
            'error_bubbling' => true,
            'admin' => $this->contactAdmin,
            'admin_name' => $this->contactAdminName
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('student', 'tsk_contact_list', array('admin' => $options['admin'], 'admin_name' => $options['admin_name'], 'entity' => 'TSKStudentBundle:Student', 'show_add' => false, 'label' => 'Select', 'list_label' => 'Select Student ...'));
        $builder->add('class', 'entity_hidden', array('my_class' => 'TSK\ClassBundle\Entity\Classes'));
        $builder->add('schedule', 'entity_hidden', array('my_class' => 'TSK\ScheduleBundle\Entity\ScheduleEntity'));
        $builder->add('start', 'date', array('required' => false, 'error_bubbling' => true));
        $builder->add('until', 'hidden', array('required' => false, 'error_bubbling' => true));
        $builder->add('status', 'choice', array(
            'choices' => array('regular' => 'Regular', 'guest' => 'Guest / Substitute'),
            'error_bubbling' => true,
            'attr' => array('class' => 'input-small')
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_schedule_roster_type';
    }
}
