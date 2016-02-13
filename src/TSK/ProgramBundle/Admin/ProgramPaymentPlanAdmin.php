<?php
namespace TSK\ProgramBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ProgramPaymentPlanAdmin extends Admin
{
    protected $translationDomain = 'TSKProgramBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name')
            ->add('paymentsData', 'tsk_payment_calculator', array())
            ->add('isActive', NULL, array('required' => false))
            ->with('Deferral Info')
            ->add('deferralDurationMonths', NULL, array('help' => 'Over how many months do we defer payments?'))
            ->add('deferralRate', 'text') // note: for all float fields we must use "text" form types ...
            ->add('deferralDistributionStrategy', 'choice', array('choices' => array('straight' => 'straight', 'accelerated' => 'accelerated')))
            ->add('recognitionRate', 'text', array('required' => false))
            ->add('recognitionCap', NULL, array('help' => 'Preview <a href="" id="previewDeferral">deferral</a>', 'attr' => array('class' => 'span5')))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('program.programName')
            ->add('deferralDurationMonths')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('program.programName')
            ->add('deferralDurationMonths')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        // $errorElement
        //     ->with('program_name')
        //         ->assertMaxLength(array('limit' => 32))
        //     ->end()
        // ;
    }
}
