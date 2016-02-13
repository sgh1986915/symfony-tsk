<?php
namespace TSK\SchoolBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class SchoolAdmin extends Admin
{
    protected $translationDomain = 'TSKSchoolBundle';
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('legacyId')
            ->add('name')
            ->add('contact', 'sonata_type_model_list', array(), array(
                'admin_code' => 'tsk.admin.contact'
            ))
            ->add('paymentMax')
            ->add('lateGraceDays')
            ->add('latePaymentCharge')
            ->with('Deferral Info')
            ->add('deferralRate')
            ->add('distributionStrategy', 'choice', array('empty_value' => '--choose', 'choices' => array('straight' => 'Straight', 'accelerated' => 'Accelerated')))
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('contact.firstName')  // This field is MANDATORY for org. filtering, see SchoolAdminExtension.php
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('contact.address1')
            ->add('contact.city')
            ->add('contact.state')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('name')
                ->assertMaxLength(array('limit' => 32))
            ->end()
        ;
    }
}
