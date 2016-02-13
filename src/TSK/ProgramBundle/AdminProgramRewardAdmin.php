<?php
namespace TSK\ProgramBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class ProgramRewardAdmin extends Admin
{
    protected $translationDomain = 'TSKProgramBundle';
    
    protected function configureFormFields(FormMapper $formMapper)
    {

        $ref = new \ReflectionClass('\TSK\ProgramBundle\Ruler\ProgramReward');
        foreach ($ref->getMethods() as $method) {
            // if ($method->getName() != '__construct') {
                $methods[$method->getName()] = $method->getName();
            // }
        }
        $formMapper
            ->add('name', 'text', array('data' => 'setEligibleProgram'))
            // ->add('description')
            ->add('method', 'choice', array(
                'choices' => $methods,
                'attr' => array('class' => 'methodPicker')
            ))
            ->add('metaData')
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
