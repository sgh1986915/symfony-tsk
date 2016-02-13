<?php
namespace TSK\RulerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use TSK\RulerBundle\Form\Type\RuleType;

class RuleContextAdmin extends Admin
{
    protected $formSubscriber;
    protected $translationDomain = 'TSKRulerBundle';
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description')
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
            ->addIdentifier('id')
            ->add('name')
            ->add('description')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
                ->assertLength(array('max' => 100))
            ->end()
        ;
    }

    // public function prePersist($ruleGroup)
    // {
    //     foreach ($ruleGroup->getRules() as $rule) {
    //         $rule->setGroup($ruleGroup);
    //     }

    //     foreach ($ruleGroup->getRewards() as $reward) {
    //         $reward->setGroup($ruleGroup);
    //     }
    // }
    // 
    // public function preUpdate($ruleGroup)
    // {
    //     foreach ($ruleGroup->getRules() as $rule) {
    //         $rule->setGroup($ruleGroup);
    //     }

    //     foreach ($ruleGroup->getRewards() as $reward) {
    //         $reward->setGroup($ruleGroup);
    //     }
    // }
}
