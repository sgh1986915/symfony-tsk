<?php
namespace TSK\PaymentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class CollectAdmin extends Admin
{
    protected $translationDomain = 'TSKPaymentBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type')
            ->add('due')
            ->add('amount') 
            ->add('balance')  
       ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('type')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('type')
            ->add('due')
            ->add('amount')
            ->add('balance')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        
    }
}
