<?php
namespace TSK\RulerBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use TSK\RulerBundle\Form\Type\RuleType;


class RuleGroupAdmin extends Admin
{
    protected $formSubscriber;
    protected $translationDomain = 'TSKRulerBundle';
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            // ->with('Rules')
            ->add('description')
            ->add('conjunction', 'choice', array(
                    'choices' => array('and' => 'and', 'or' => 'or'), 
                    'attr' => array('class' => 'conjunctionPicker input-mini')
            ))
            ->add('rules', 'collection', array(
                    'type' => 'tsk_rule', 
                    'options' => array('label_render' => true, 'label' => 'Rule'),
                    'required' => false,
                    'attr' => array('class' => 'foo'),
                    'widget_controls_attr' => array('class' => 'foo'),
                    'widget_control_group_attr' => array('class' => 'foo'),
                    'by_reference' => false,
                    'widget_add_btn' => array('icon' => 'minus-sign'),
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
            ->with('Rewards')
            ->add('rewards', 'collection', array(
                    'type' => 'tsk_reward', 
                    'options' => array('label' => 'Reward'),
                    'attr' => array('class' => 'foo'),
                    'required' => false,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('context')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('context')
            ->add('description')
            ->add('rules')
            ->add('rewards')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('conjunction')
                ->assertLength(array('max' => 100))
            ->end()
        ;
    }

    public function prePersist($ruleGroup)
    {
        foreach ($ruleGroup->getRules() as $rule) {
            $rule->setGroup($ruleGroup);
        }

        foreach ($ruleGroup->getRewards() as $reward) {
            $reward->setGroup($ruleGroup);
        }
    }

    public function preUpdate($ruleGroup)
    {
        foreach ($ruleGroup->getRules() as $rule) {
            $rule->setGroup($ruleGroup);
        }

        foreach ($ruleGroup->getRewards() as $reward) {
            $reward->setGroup($ruleGroup);
        }
    }
}
