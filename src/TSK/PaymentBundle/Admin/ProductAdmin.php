<?php
namespace TSK\PaymentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class ProductAdmin extends Admin
{
    protected $translationDomain = 'TSKPaymentBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('incomeType')
            ->add('description') 
            ->add('price') 
            ->add('isActive') 
       ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('incomeType')
            ->add('description')
            ->add('price')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        // $errorElement
        //     ->with('name')
        //         ->assertMaxLength(array('limit' => 32))
        //     ->end()
        // ;
    }
}
