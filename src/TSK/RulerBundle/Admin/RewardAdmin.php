<?php
namespace TSK\RulerBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class RewardAdmin extends Admin
{
    protected $translationDomain = 'TSKRulerBundle';
    
    protected function getRewardMethods()
    {
     $ref = new \ReflectionClass('\TSK\PaymentBundle\Ruler\DiscountReward');
        foreach ($ref->getMethods() as $method) {
            if ($method->getName() != '__construct') {
                $methods['discount'][$method->getName()] = $method->getName();
            }
        }
        return $methods;
    }
    protected function configureFormFields(FormMapper $formMapper)
    {
   
        $formMapper
            ->add('name')
            // ->add('description')
            ->add('method', 'choice', array(
                'choices' => $this->getRewardMethods(),
                'attr' => array('class' => 'methodPicker')
            ))
            ->add('metaData')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('description')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('description')
            ->add('method')
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
}
