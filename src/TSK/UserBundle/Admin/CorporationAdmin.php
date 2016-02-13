<?php
namespace TSK\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CorporationAdmin extends Admin
{
    protected $translationDomain = 'TSKUserBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('legal_name', 'text', array('required' => true))
            ->add('tax_id', 'text', array('required' => true))
            ->add('dba', 'text', array('label' => 'DBA', 'required' => false))
            ->add('account_num', 'text', array('required' => false))
            ->add('routing_num', 'text', array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('legal_name')
            ->add('tax_id')
            ->add('dba')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('legal_name')
            ->addIdentifier('tax_id')
            ->add('account_num')
            ->add('routing_num')
            ->add('dba')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('legal_name')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }
}
