<?php
namespace TSK\PaymentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class PaymentMethodsAdmin extends Admin
{
    protected $translationDomain = 'TSKPaymentBundle';
    private $orgSessionKey;
    public function setOrgSessionKey($orgSessionKey)
    {
        $this->orgSessionKey = $orgSessionKey;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('payment_type')
            ->add('isRecurring') 
            ->add('isReceivable') 
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('payment_type')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('payment_type')
            ->add('isRecurring')
            ->add('isReceivable')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }

    public function prePersist($object)
    {
    }
}
