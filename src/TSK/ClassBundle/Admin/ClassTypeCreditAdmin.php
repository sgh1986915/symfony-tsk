<?php
namespace TSK\ClassBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ClassTypeCreditAdmin extends Admin
{
    protected $translationDomain = 'TSKClassBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with("Class")
            ->add('class', 'sonata_type_model', array('required' => true))
            ->add('classType', 'sonata_type_model', array('required' => true))
            ->add('value')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('class')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('class.title')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('value')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }
}
