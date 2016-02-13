<?php
namespace TSK\RulerBundle\Admin;


use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;


class FactAdmin extends Admin
{
    protected $translationDomain = 'TSKRulerBundle';
    
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('description')
            ->add('factType', 'choice', array(
                'choices' => array(
                    'integer' => 'integer',
                    'string' => 'string',
                    'date' => 'date',
                    'datetime' => 'datetime',
                    'callback' => 'callback'
                ),
                'empty_value' => 'Choose a fact type'
            ))
            ->add('method')
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
            ->add('factType')
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
