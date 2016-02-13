<?php
namespace TSK\ContractBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use TSK\StudentBundle\Form\Type\PaymentPlanSelectorFormType;
use TSK\StudentBundle\Form\EventListener\DrawPaymentMethodSubscriber;
use TSK\UserBundle\Form\Type\ContactSearchType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

class ContractUpgradeFormType extends AbstractType {
    private $session;
    private $contract;
    
    public function setContract($contract)
    {
        $this->contract = $contract;
    }

    public function __construct($session, $orgSessionKey)
    {
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $org = $this->session->get($this->orgSessionKey);
        switch ($options['flowStep']) {
            case 1:
                $builder->add('discountLevel', 'choice', array(
                    'choices' => array(
                        'individual' => 'Single',
                        'family_member' => 'Family Member'
                    ),
                    'label' => 'Discount Type',
                ));
                break;
            case 2:
                $builder->add('trainingFamilyMember', 'entity', array(
                    'label' => 'Training Family Member',
                    'required' => false,
                    'class' => 'TSKStudentBundle:TrainingFamilyMembers',
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('s')
                            ->join('s.primaryStudent', 'p')
                            ->join('p.contact', 'c', 'WITH', 'c.lastName=:lastName AND c.address1=:address1 and c.state=:state and c.postalCode=:postal_code')
                            ->setParameter(':lastName', $options['student']->getContact()->getLastName())
                            ->setParameter(':address1', $options['student']->getContact()->getAddress1())
                            ->setParameter(':state', $options['student']->getContact()->getState())
                            ->setParameter(':postal_code', $options['student']->getContact()->getPostalCode());
                    }
                    )
                );

                break;
            case 3:
                $builder->add('program', 'entity', array(
                    'class' => 'TSKProgramBundle:Program',
                    'property' => 'select_label',
                    'empty_value'   => '-- Select a program --',
                    'query_builder' => function(EntityRepository $er) use ($options) {

                            $filters = array_keys($options['programDiscountTypeFilters']->toArray());
                            // $programExcludes = array_keys($options['programExcludes']->toArray());
                            $programExcludes = array();
                            $pSubClause = (count($programExcludes)) ? '1=0' : '1=1';
                            $subClause = (count($filters)) ? '1=0' : '1=1';

                            return $er->createQueryBuilder('p')
                                ->where(':school MEMBER OF p.schools')
                                ->andWhere('p.isActive=1')
                                ->andWhere('p.expirationDate > :today OR p.expirationDate IS NULL')
                                ->andWhere('(p.discountType IN (:filters) OR ' . $subClause . ')')
                                ->andWhere('(p.id NOT IN (:excludes) OR ' . $pSubClause . ')')
                                ->setParameter(':school', $options['school'])
                                ->setParameter(':today', time())
                                ->setParameter(':filters', $filters)
                                ->setParameter(':excludes', $programExcludes)
                                ;
                        }
                    ));
                break;

            case 4:
                $builder->add('programPaymentPlan', 'entity', array(
                    'mapped' => true,
                    'label' => 'Choose a Payment Plan',
                    'required' => false,
                    'empty_value' => 'Choose a payment plan',
                    'class' => 'TSKProgramBundle:ProgramPaymentPlan',
                    'property' => 'selectLabel',
                    'query_builder' => function(EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('p')
                        ->where('p.program=:program')
                        ->setParameter(':program', $options['program']);
                    }
                ));
                break;
            case 5:
            // ld($options['programPaymentPlan']);
            $pd = $options['programPaymentPlan']->getPaymentsData();
            $json = $pd['paymentsData'];
            $obj = json_decode($json);
            // ld($pd);
            // $obj->payments = array_fill(0, count($obj->payments), 0);
            $obj->credit = $options['contract']->getCreditDue();

                $builder->add('paymentPlanCustomizedPayments', 'tsk_payment_calculator', array(
                    'mapped' => true, 
                    'data' => array('paymentsData' => json_encode($obj)),
                    // 'data' => $pd,
                    'label' => 'blahblah', 
                    'hide_data' => false, 
                    'hide_button' => false
                ));
                break;
            case 6:
                $builder->add('please_confirm', 'checkbox', array('mapped' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Please confirm these terms.'));
                break;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'flowStep' => 1,
            'data_class' => 'TSK\ContractBundle\Form\Model\ContractUpgrade', 
            'contract' => null,
            'student' => null,
            'school' => null,
            'program' => null,
            'programPaymentPlan' => null,
            'programDiscountTypeFilters' => null
        ));
    }

    public function getName() {
        return 'contractUpgrade';
    }
}
