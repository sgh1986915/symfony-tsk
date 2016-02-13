<?php
namespace TSK\ContractBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use TSK\StudentBundle\Form\Type\PaymentPlanSelectorFormType;
use TSK\StudentBundle\Form\EventListener\DrawPaymentMethodSubscriber;
use TSK\UserBundle\Form\Type\ContactSearchType;

class ContractModifierFormType extends AbstractType {
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
                    $builder->add('trainingFamilyMembers', 'text', array('mapped' => false, 'required' => false, 'help_inline' => 'Active students with active contract and has no discount matching last name or street address'));
                break;

            case 2:
                    $builder->add('discount_level', 'choice', array(
                        'mapped' => false, 
                        'choices' => array(
                            'indvidual' => 'Individual',
                            'family_member' => 'Family Member',
                            'veteran' => 'Veteran',
                            'student' => 'Student'
                        ),
                        'label' => 'Program Type',
                    ));
                break;

            case 7:
                $builder->add('payInFull', 'checkbox', array('required' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Student will pay in full (skip recurring payment details)?', 'attr' => array('class' => 'cbxPayInFull')));
                $builder->add('billee_payment_method', 'entity', array(
                        'required' => false,
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
                // $builder->add('studentDateOfBirth', 'date', array('widget' => 'single_text', 'label' => 'Date of Birth', 'mapped' => true, 'required' => true, 'years' => range(1920, date('Y'))));
                $builder->add('cc_expiration_date', 'date', array('years' => range(date('Y'), date('Y') + 10), 'widget' => 'choice', 'days' => array(1), 'widget_controls_attr' => array('class' => 'credit recurs'), 'render_optional_text' => false,  'label_attr' => array('class' => 'credit recurs'), 'label' => 'Credit Card Expiration Date', 'required' => false, 'attr' => array('class' => 'credit recurs')));
                $builder->add('cvv_number', NULL, array('render_optional_text' => false,  'label_attr' => array('class' => 'credit recurs'), 'label' => 'CVV Number', 'required' => false, 'attr' => array('class' => 'credit recurs')));
                $builder->add('routing_number', NULL, array('label_attr' => array('class' => 'ach recurs'), 'label' => 'Routing Number', 'render_optional_text' => false, 'required' => false, 'attr' => array('class' => 'ach recurs')));
                $builder->add('account_number', NULL, array('label_attr' => array('class' => 'ach recurs'), 'label' => 'Account Number', 'render_optional_text' => false, 'required' => false, 'attr' => array('class' => 'ach recurs')));
                break;
            case 8:
                $builder->add('please_confirm', 'checkbox', array('mapped' => false, 'widget_checkbox_label' => 'widget', 'label' => 'Please confirm these terms.'));
                break;
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'flowStep' => 1,
            'data_class' => 'TSK\ContractBundle\Form\Model\ContractModifier', 
            'contract' => null
        ));
    }

    public function getName() {
        return 'contractModifier';
    }
}
