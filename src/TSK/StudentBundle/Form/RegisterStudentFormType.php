<?php
namespace TSK\StudentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use TSK\StudentBundle\Form\Type\PaymentPlanSelectorFormType;
use TSK\StudentBundle\Form\EventListener\DrawPaymentMethodSubscriber;
use TSK\UserBundle\Form\Type\ContactSearchType;

class RegisterStudentFormType extends AbstractType {
    private $session;
    private $orgSessionKey;
    private $studentContactAdmin;
    private $emergencyContactAdmin;
    private $billeeContactAdmin;
    
    public function __construct($studentContactAdmin, $emergencyContactAdmin, $billeeContactAdmin)
    {
        $this->studentContactAdmin = $studentContactAdmin;
        $this->emergencyContactAdmin = $emergencyContactAdmin;
        $this->billeeContactAdmin = $billeeContactAdmin;
    }

    public function setSession($session, $orgSessionKey)
    {
        $this->session = $session;
        $this->orgSessionKey = $orgSessionKey;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) 
    {
        $org = $this->session->get($this->orgSessionKey);
        switch ($options['flowStep']) {
            case 1:
                $builder->add('studentContact', 'tsk_contact_list', array('admin' => $this->studentContactAdmin, 'label' => 'Find or Create Student', 'show_edit' => true));
                break;
            case 2:
                $builder->add('emergencyContactContact', 'tsk_contact_list', array('admin' => $this->emergencyContactAdmin, 'admin_name' => 'tsk.admin.contact_emergency', 'label' => 'Find or Create Emergency Contact', 'show_edit' => true));
                break;

            case 3:
                    $builder->add('discountLevel', 'choice', array('label' => 'Discount Level', 'choices' => array('individual' => 'Individual', 'family' => 'Family Member'), 'expanded' => true, 'mapped' => true, 'required' => false, 'help_inline' => 'Active students with active contract and has no discount matching last name or street address', 'multiple' => false));
                break;
            case 4:
                    if (!empty($options['student'])) {
                        $builder->add('trainingFamilyMember', 'entity', array(
                            'mapped' => true,
                            'label' => 'Training Family Member',
                            'required' => false,
                            'class' => 'TSKStudentBundle:TrainingFamilyMembers',
                            'query_builder' => function(EntityRepository $er) use ($options) {
                                return $er->createQueryBuilder('s')
                                    ->join('s.primaryStudent', 'p')
                                    ->join('p.contact', 'c', 'WITH', 'c.lastName=:lastName AND c.address1=:address1 and c.state=:state and c.postalCode=:postal_code')
                                    ->setParameter(':lastName', $options['student']->getLastName())
                                    ->setParameter(':address1', $options['student']->getAddress1())
                                    ->setParameter(':state', $options['student']->getState())
                                    ->setParameter(':postal_code', $options['student']->getPostalCode());
                            }
                            )
                        );
                    } // else {
                        // $builder->add('tfms', 'text', array('mapped' => false));
                    // }
                break;
            case 5:
                $builder->add('program', 'entity', array(
                        'mapped' => true,
                        'empty_value' => 'Choose a program',
                        'class' => 'TSKProgramBundle:Program',
                        'property' => 'select_label',
                        'query_builder' => function(EntityRepository $er) use ($options) {
                            $filters = array_keys($options['programDiscountTypeFilters']->toArray());
                            $programExcludes = array_keys($options['programExcludes']->toArray());
                            $pSubClause = (count($programExcludes)) ? '1=0' : '1=1';
                            $subClause = (count($filters)) ? '1=0' : '1=1';
                            return $er->createQueryBuilder('p')
                                ->where(':school MEMBER OF p.schools')
                                ->andWhere('p.isActive=1')
                                ->andWhere('p.expirationDate > :today OR p.expirationDate IS NULL')
                                ->andWhere('(p.discountType IN (:filters) OR ' . $subClause . ')')
                                ->andWhere('(p.id NOT IN (:excludes) OR ' . $pSubClause . ')')
                                ->setParameter(':today', time())
                                ->setParameter(':filters', $filters)
                                ->setParameter(':excludes', $programExcludes)
                                ->setParameter(':school', $options['school']);
                        }
                    )
                );
                break;
            case 6:
                $builder->add('programPaymentPlan', new PaymentPlanSelectorFormType(), array(
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
                $builder->add('paymentPlanCustomizedPayments', 'tsk_payment_calculator', array('mapped' => true, 'label' => ' ', 'hide_data' => true, 'hide_button' => true));
                break;
            case 7:
                $builder->add('copyFromStudent', 'checkbox', array('mapped' => true, 'required' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Copy Student Details into Billee?', 'attr' => array('class' => 'copyStudentToBillee')));
                $builder->add('billeeContact', 'tsk_contact_list', array('label' => 'Billee Details', 'admin' => $this->billeeContactAdmin, 'show_edit' => true));
                break;
            // case 6:
                // $builder->add('copyStudentIntoEmerg', 'checkbox', array('mapped' => true, 'required' => false, 'widget_control_group' => false,  'show_legend' => true, 'render_optional_text' => false, 'label' => 'Copy Student Details into Emergency Contact?', 'widget_checkbox_label' => 'widget', 'attr' => array('class' => 'copyStudentToEmergency')));
                // $builder->add('emergencyContactContact', 'tsk_contact_list', array('admin' => $this->contactAdmin));
                // break;

            case 8:
                $builder->add('payInFull', 'checkbox', array('required' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Student will pay in full (skip recurring payment details)?', 'attr' => array('class' => 'cbxPayInFull')));
                $builder->add('paymentMethod', 'entity', array(
                        'required' => false,
                        'label' => 'Payment Method',
                        'empty_value' => 'Choose a payment method',
                        'class' => 'TSKPaymentBundle:PaymentMethod',
                        'property' => 'name',
                        'query_builder' => function(EntityRepository $er) use ($org) {
                            return $er->createQueryBuilder('p')
                                ->where('p.organization = :org')
                                ->andWhere('p.isRecurring = 1')
                                ->setParameter(':org', $org);
                        }
                    )
                );
                $builder->add('cc_num', NULL, array('required' => false, 'render_optional_text' => false, 'label' => 'Credit Card Number', 'label_attr' => array('class' => 'credit recurs'), 'attr' => array('class' => 'credit recurs')));
                $builder->add('cc_expiration_date', 'date', array('years' => range(date('Y'), date('Y') + 10), 'widget' => 'choice', 'days' => array(1), 'widget_controls_attr' => array('class' => 'credit recurs'), 'render_optional_text' => false,  'label_attr' => array('class' => 'credit recurs'), 'label' => 'Credit Card Expiration Date', 'required' => false, 'attr' => array('class' => 'credit recurs')));
                $builder->add('cvv_number', NULL, array('render_optional_text' => false,  'label_attr' => array('class' => 'credit recurs'), 'label' => 'CVV Number', 'required' => false, 'attr' => array('class' => 'credit recurs')));
                $builder->add('routing_number', NULL, array('label_attr' => array('class' => 'ach recurs'), 'label' => 'Routing Number', 'render_optional_text' => false, 'required' => false, 'attr' => array('class' => 'ach recurs')));
                $builder->add('account_number', NULL, array('label_attr' => array('class' => 'ach recurs'), 'label' => 'Account Number', 'render_optional_text' => false, 'required' => false, 'attr' => array('class' => 'ach recurs')));
                break;
            case 9:
                $builder->add('please_confirm', 'checkbox', array('mapped' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Please confirm these terms.'));
                break;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'flowStep' => 1,
            'data_class' => 'TSK\StudentBundle\Form\Model\StudentRegistration', // should point to your user entity
            'school' => null,
            'student' => null,
            'program' => null,
            'discountLevel' => null,
            'studentDateOfBirth' => null,
            'programDiscountTypeFilters' => null,
            'programExcludes' => null
        ));
    }

    public function getName() {
        return 'registerStudent';
    }
}
