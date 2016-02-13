<?php
namespace TSK\ContractBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;


class UpgradeContractType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\ContractBundle\Form\Model\ContractUpgrade',
            'school' => null
        ));
    }

    public function getName()
    {
        return 'tsk_contract_upgrade';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contract', 'entity_hidden', array(
            'my_class' => 'TSK\ContractBundle\Entity\Contract')
        );
        $builder->add('program', 'entity', array(
                'class' => 'TSKProgramBundle:Program',
                'property' => 'select_label',
                'empty_value'   => '-- Select a program --',
                'query_builder' => function(EntityRepository $er) use ($options) {
                    $filters = array();
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

        $factory = $builder->getFormFactory();
        $refreshPaymentPlans = function($form, $program) use ($factory) {
            $form->add($factory->createNamed('programPaymentPlan', 'entity', array(
                'class' => 'TSKProgramBundle:ProgramPaymentPlan',
                'property' => 'name',
                'query_builder' => function (EntityRepository $er) use ($program) {
                    $qb = $er->createQueryBuilder('pp');
                    if ($program instanceof Program) {
                        $qb->where('pp.program = :program')
                            ->setParameter('program', $program);
                    } else if (is_numeric($program)) {
                        $qb->where('program.id = :program')
                            ->setParameter('program', $program);
                    } else {
                        $qb->where('program.name = :program')
                            ->setParameter('program', null);
                    }
                    return $qb;
                }
            )));
            $form->add($factory->createNamed('paymentPlanCustomizedPayments', 'tsk_payment_calculator', array('mapped' => true, 'label' => ' ', 'hide_data' => true, 'hide_button' => true))    ;
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (DataEvent $event) use ($refreshPaymentPlans)
        {
            $form = $event->getForm();
            $data = $event->getData();

            if($data == null)
                return;               

            if($data instanceof Program){
                if($data->getId()) { //An existing Program
                    $refreshPaymentPlans($form, $data->getProgram()->getProgramPaymentPlan());
                }else{               //A new PaymentPlan
                    $refreshPaymentPlans($form, null);
                }
            }
        });

        $builder->addEventListener(FormEvents::PRE_BIND, function (DataEvent $event) use ($refreshPaymentPlans)
        {
            $form = $event->getForm();
            $data = $event->getData();

            if(array_key_exists('program', $data)) {
                $refreshPaymentPlans($form, $data['program']);
            }
        });

    }
}
