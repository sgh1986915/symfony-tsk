<?php
namespace TSK\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CorporationContactAdmin extends Admin
{
    protected $translationDomain = 'TSKUserBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            // ->add('organization', null, array('empty_value' => '-- select organization'))
            ->add('contact', 'sonata_type_model_list')
            ->add('corporation', 'sonata_type_model_list')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('contact.firstName')
            ->add('contact.lastName')
            ->add('corporation.dba')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('contact.firstName')
            ->add('contact.lastName')
            ->add('corporation.dba')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        // $errorElement
        //     ->with('firstName')
        //         ->assertMaxLength(array('limit' => 32))
        //     ->end()
        // ;
    }
}
