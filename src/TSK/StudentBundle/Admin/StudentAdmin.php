<?php
namespace TSK\StudentBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class StudentAdmin extends Admin
{
    protected $translationDomain = 'TSKStudentBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('contact', 'sonata_type_model_list', array(), array(
                'admin_code' => 'tsk.admin.contact'
            ))
            ->add('rank')
            ->add('studentStatus')
            ->add('eligibleRanks', NULL, array('required' => false))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('contact.firstName')
            ->add('contact.lastName')
            ->add('studentStatus')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->addIdentifier('contact.firstName', null, array('label' => 'First Name'))
            ->addIdentifier('contact.lastName', null, array('label' => 'Last Name'))
            ->add('contact.email', null, array('label' => 'Email'))
            // ->add('contact.schools', null, array('label' => 'School'))
            ->add('studentStatus', null, array('label' => 'Status'))
            ->add('rank')
            ->add('eligibleRanks')
            ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'contract' => array('template' => 'TSKStudentBundle:CRUD:list__action_contract.html.twig'),
                        'promote' => array('template' => 'TSKStudentBundle:CRUD:list__action_promote.html.twig'),
                    )
                )
            )
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
/*
        $errorElement
            ->with('programName')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
*/
    }
}
