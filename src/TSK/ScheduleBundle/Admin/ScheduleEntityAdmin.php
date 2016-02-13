<?php
namespace TSK\ScheduleBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

use TSK\ScheduleBundle\Form\Type\ScheduleOptionsType;

class ScheduleEntityAdmin extends Admin
{
    protected $translationDomain = 'TSKScheduleBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            // ->add('user')
            // ->add('school')
            ->add('category', NULL, array('attr' => array('class' => 'catpicker')))
            ->add('classes', null, array('required' => false, 'attr' => array('class' => 'classpicker')))
            // ->add('instructors', null, array('required' => false))
            // ->add('capacity')
            ->add('title')
            ->add('startAt', 'dateTimePicker')
            ->add('endAt', 'dateTimePicker', array('help' => 'should be same day as start time'))
            ->add('options', 'tsk_schedule_options', array('label' => false, 'required' => false))
            ->add('location')
            ->add('description')
            // ->add('duration')
            ->add('rrule', 'hidden', array('attr' => array('class' => 'rruleElem')))
            // ->add('revision')
            // ->add('guests')
            // ->add('priority')
            // ->add('isTransparent')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('title')
            ->add('startAt', 'doctrine_orm_date', array('inpupt_type' => 'foo'))
            ->add('category')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title')
            ->add('category')
            ->add('startAt')
            ->add('endAt')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('title')
                ->assertMaxLength(array('limit' => 100))
            ->end()
        ;
    }
}
