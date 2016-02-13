<?php
namespace TSK\ProgramBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Doctrine\ORM\EntityRepository;
use TSK\ProgramBundle\Entity\ProgramPaymentPlan;

class ProgramAdmin extends Admin
{
    protected $translationDomain = 'TSKProgramBundle';
    protected $organization;

    public function getOrganization($session, $orgSessionKey)
    {
        $this->organization = $session->get($orgSessionKey);
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $org = $this->organization;
        $formMapper
            ->add('programName')
            ->add('membership_type', 'entity', array(
                'class' => 'TSKProgramBundle:MembershipType',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($org) {
                    return $er->createQueryBuilder('u')
                            ->where('u.organization=:org_id')
                            ->setParameter(':org_id', $org);
                }
                    ))
            ->add('programType', 'entity', array(
                'class' => 'TSKProgramBundle:ProgramType',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($org) {
                    return $er->createQueryBuilder('u')
                            ->where('u.organization=:org_id')
                            ->setParameter(':org_id', $org);
                }
                    ))
            ->add('discountType', 'entity', array(
                'class' => 'TSKPaymentBundle:DiscountType',
                'property' => 'name',
                'query_builder' => function(EntityRepository $er) use ($org) {
                    return $er->createQueryBuilder('u')
                            ->where('u.organization=:org_id')
                            ->setParameter(':org_id', $org);
                }
                    ))
            ->add('numTokens', NULL, array('help' => 'Number of tokens offered by this program'))
            ->add('durationDays', NULL, array('help' => 'Length of time offered by this program in days'))
            ->add('paymentPlans', 'sonata_type_collection', 
                array(
                    'required' => false,
                    'by_reference' => false
                ),
                array(
                'edit' => 'inline', 
                'inline' => 'table',
                'allow_delete' => true
                ))
            ->add('description')
            ->add('legalDescription')
            ->add('expiration_date', 'date', array('widget' => 'single_text', 'required' => false, 'input' => 'timestamp', 'help' => 'Leave blank (mm/dd/yyyy) for no expiry'))
            ->add(  'schools', 'entity', array(
                    'class' => 'TSKSchoolBundle:School',
                    'property' => 'name',
                    'multiple' => true,
                    'query_builder' => function(EntityRepository $er)  use ($org) {
                        // Example of an inner join to filter on organization
                        return $er->createQueryBuilder('s')
                                    ->innerJoin('s.contact', 'c', 'WITH', 'c.organization = :org_id')
                                    ->setParameter(':org_id', $org);
                    }
            ))
            ->add('isActive', NULL, array('required' => false))
            // ->with('Program Rules')
            // ->add('groups', 'sonata_type_collection',
            //     array(
            //         'required' => false,
            //         'by_reference' => false,
            //         'cascade_validation' => true
            //     ),
            //     array(
            //     'edit' => 'inline',
            //     'inline' => 'table',
            //     'allow_delete' => true
            //     )
            // )
            // ->with('Rewards')
            // ->add('rewards', 'sonata_type_collection',
            //     array(
            //         'required' => false,
            //         'by_reference' => false
            //     ),
            //     array(
            //     'edit' => 'inline',
            //     'inline' => 'table',
            //     'allow_delete' => true
            //     )
            // )
 

        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('programName')
            ->add('programType')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('programName')
            ->add('programType')
            ->add('discountType')
            ->add('expiration_date', 'date')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $errorElement
            ->with('programName')
                ->assertMaxLength(array('limit' => 232))
            ->end()
        ;
    }

    public function prePersist($program)
    {
        foreach ($program->getPaymentPlans() as $paymentPlan) {
            $paymentPlan->setProgram($program);
        }

        // // Save rules / rule groups
        // foreach ($program->getGroups() as $group) {
        //     $group->setProgram($program);
        //     if ($group->getRules()) {
        //         foreach ($group->getRules() as $rule) {
        //             $rule->setGroup($group);
        //         }
        //     }
        // }
        // $program->setGroups($program->getGroups());

        // // Save rewards
        // foreach ($program->getRewards() as $reward) {
        //     $reward->setProgram($program);
        // }
        // $program->setRewards($program->getRewards());

    }

    public function preUpdate($program)
    {
        foreach ($program->getPaymentPlans() as $paymentPlan) {
            $paymentPlan->setProgram($program);
        }
        $program->setPaymentPlans($program->getPaymentPlans());

        // // Save rules / rule groups
        // foreach ($program->getGroups() as $group) {
        //     $group->setProgram($program);
        //     if ($group->getRules()) {
        //         foreach ($group->getRules() as $rule) {
        //             $rule->setGroup($group);
        //         }
        //     }
        // }
        // $program->setGroups($program->getGroups());

        // // Save rewards
        // foreach ($program->getRewards() as $reward) {
        //     $reward->setProgram($program);
        // }
        // $program->setRewards($program->getRewards());

    }
}
