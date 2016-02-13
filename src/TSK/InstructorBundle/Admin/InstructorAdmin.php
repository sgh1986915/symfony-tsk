<?php
namespace TSK\InstructorBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class InstructorAdmin extends Admin
{
    protected $translationDomain = 'TSKInstructorBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('contact', 'sonata_type_model_list')
            ->add('qualifications')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('contact.firstName')
            ->add('contact.lastName')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('contact.firstName', null, array('label' => 'First Name'))
            ->addIdentifier('contact.lastName', null, array('label' => 'Last Name'))
            ->add('contact.email', null, array('label' => 'Email'))
            ->add('qualifications')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
/*
        $errorElement
            ->with('programName')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
*/
    }
}
