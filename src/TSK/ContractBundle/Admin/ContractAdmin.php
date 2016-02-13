<?php
namespace TSK\ContractBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;

class ContractAdmin extends Admin
{
    protected $organization;
    protected $school;
    protected $translationDomain = 'TSKContractBundle';
    public function getOrgSchool($session, $orgSessionKey, $schoolSessionKey)
    {
        $this->organization = $session->get($orgSessionKey);
        $this->school = $session->get($schoolSessionKey);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $org = $this->organization;
        $school = $this->school;
        $formMapper
            ->add('school', 'entity', array(
                'class' => 'TSKSchoolBundle:School',
                'query_builder' => function(EntityRepository $er) use ($org) {
                    return $er->createQueryBuilder('u')
                        ->join('u.contact', 'c', 'WITH', 'c.organization=:org')
                        ->setParameter(':org', $org);
                }
            ))
            ->add('program')
            ->add('payment_terms', 'tsk_payment_calculator', array('required' => true))
            // ->add('students', 'tsk_contact_list')
            // ->add('students', 'genemu_jqueryselect2_hidden', array(
            //     'configs' => array(
            //         'multiple' => true
            //     )
            // ))
            ->add('students', 'genemu_jqueryselect2_entity', array(
                'class' => 'TSKStudentBundle:Student',
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) use ($org, $school) {
                    return $er->createQueryBuilder('u')
                        ->join('u.contact', 'c', 'WITH', 'c.organization=:org')
                        // ->join('c.schools', 's')
                        ->where(':school MEMBER OF c.schools')
                        ->setParameter(':org', $org)
                        ->setParameter(':school', $school);
            }
            ))
            ->add('contractStartDate', 'date', array('widget' => 'single_text'))
            ->add('contractExpiry', 'date', array('widget' => 'single_text'))
            ->add('contractNumTokens')
            ->add('rolloverTokens')
            ->add('rolloverDays')
            ->add('isActive', null, array('required' => false))
            ->with('Deferral Info')
            ->add('deferralRate')
            ->add('deferralDurationMonths')
            ->add('deferralDistributionStrategy', 'choice', array('required' => false, 'empty_value' => '--choose', 'choices' => array('straight' => 'Straight', 'accelerated' => 'Accelerated')))
;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('payment_terms')
            ->add('isActive')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('students')
            ->add('program.membershipType')
            ->add('amount')
            ->add('contractStartDate')
            ->add('contractExpiry')
            ->add('isActive')
            ->add('createdDate')
            ->add('updateDate')
            // ->add('createdBy')
            // ->add('updateBy')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'summary' => array('template' => 'TSKContractBundle:CRUD:list__summary.html.twig'),
                    'print' => array('template' => 'TSKContractBundle:CRUD:list__print.html.twig')
                )
            ))
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        // if ($object->getPaymentMax()) {
        //     $errorElement
        //         ->with('paymentMax')
        //             ->assertMin(array(
        //                             'limit' => ($object->getAmount() / $object->getDeferralDurationMonths()),
        //                         )
        //             )
        //         ->end();
        // }
        // $errorElement
        //     ->with('paymentMax')
        //         ->assertMaxLength(array('limit' => 32))
        //     ->end()
        // ;
    }
}
