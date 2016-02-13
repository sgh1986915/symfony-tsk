<?php
namespace TSK\RulerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use TSK\RulerBundle\Form\Type\RuleType;

class RuleCollectionAdmin extends Admin
{
    protected $formSubscriber;
    protected $translationDomain = 'TSKRulerBundle';
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('context', 'sonata_type_model')
            ->add('description')
            ->with('Rules')
            ->add('ruleGroups', 'sonata_type_collection', array(
                    'required' => false,
                    'by_reference' => false,
                    'help' => 'Add a collection of rules along with a collection of rewards to be delivered if those rules are satisfied',
                    'label_render' => true,
                    'widget_controls' => false,
                    'widget_control_group' => false,
                ), array(
                    'inline' => 'standard',
                    'edit' => 'inline',
                    'allow_delete' => true,
                )
            )
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('context')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('context')
            ->add('description')
            ->add('ruleGroups')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('context')
                ->assertLength(array('max' => 100))
            ->end()
        ;
    }

    public function prePersist($ruleCollection)
    {
        foreach ($ruleCollection->getRuleGroups() as $ruleGroup) {
            $ruleGroup->setCollection($ruleCollection);
            
            foreach ($ruleGroup->getRules() as $rule) {
                $rule->setGroup($ruleGroup);
            }
            
            foreach ($ruleGroup->getRewards() as $reward) {
                $reward->setGroup($ruleGroup);
            }
        }
    }

    public function preUpdate($ruleCollection)
    {
        foreach ($ruleCollection->getRuleGroups() as $ruleGroup) {
            $ruleGroup->setCollection($ruleCollection);
            
            foreach ($ruleGroup->getRules() as $rule) {
                $rule->setGroup($ruleGroup);
            }
            
            foreach ($ruleGroup->getRewards() as $reward) {
                $reward->setGroup($ruleGroup);
            }
        }
    }
}
