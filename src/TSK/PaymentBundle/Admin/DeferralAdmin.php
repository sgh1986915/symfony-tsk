<?php
namespace TSK\PaymentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class DeferralAdmin extends Admin
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
            ->add('paymentTerm')
            ->add('programLength') 
            ->add('defermentRate') 
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('paymentTerm')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('paymentTerm')
            ->add('programLength')
            ->add('defermentRate')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('paymentTerm')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }
}
