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


class ContractFreezeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\ContractBundle\Form\Model\ContractFreeze',
        ));
    }

    public function getName()
    {
        return 'tsk_contract_freeze';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('contract', 'entity_hidden', array(
            'my_class' => 'TSK\ContractBundle\Entity\Contract')
        );
        $builder->add('student', 'entity_hidden', array(
            'my_class' => 'TSK\StudentBundle\Entity\Student') 
        );
        $builder->add('startDate', 'date', array('label' => 'Start Date', 'widget' => 'single_text', 'data' => new \DateTime()));
        $builder->add('endDate', 'date', array('label' => 'End Date', 'widget' => 'single_text', 'data' => new \DateTime()));
    }
}
