<?php
namespace TSK\PaymentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class DiscountAdmin extends Admin
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
            ->add('discountType') 
       ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('discountType')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('discountType')
            // ->add('amount')
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

    public function prePersist($discount)
    {
        // Save rules / rule groups
        foreach ($discount->getGroups() as $group) {
            $group->setDiscount($discount);
            if ($group->getRules()) {
                foreach ($group->getRules() as $rule) {
                    $rule->setGroup($group);
                }
            }
        }

        // Save rewards
        foreach ($discount->getRewards() as $reward) {
            $reward->setDiscount($discount);
        }

    }

    public function preUpdate($discount)
    {
        // Save rules / rule groups
        foreach ($discount->getGroups() as $group) {
            $group->setDiscount($discount);
            if ($group->getRules()) {
                foreach ($group->getRules() as $rule) {
                    $rule->setGroup($group);
                }
            }
        }
        $discount->setGroups($discount->getGroups());

        // Save rewards
        foreach ($discount->getRewards() as $reward) {
            $reward->setDiscount($discount);
        }
        $discount->setRewards($discount->getRewards());

    }

}
