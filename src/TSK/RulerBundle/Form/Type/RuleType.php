<?php
namespace TSK\RulerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class RuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $org = '';
        $builder
            ->add('fact', 'entity', array(
                        'attr' => array('class' => 'factPicker input-medium'),
                        'class' => 'TSK\RulerBundle\Entity\Fact',
                        'property' => 'label',
                        'empty_value' => '-- choose',
                        'query_builder' => function(EntityRepository $er) use ($org, $options) {
                            return $er->createQueryBuilder('u');
                        }
                        )
                 )
            ->add('comparator', 'choice', array(
                        'attr' => array('class' => 'input-large'),
                        'choices' => array(
                            'greaterThan' => '(>) Greater Than',
                            'greaterThanOrEqualTo' => '(>=) Greater Than or Equal To',
                            'lessThan' => '(<) Less Than',
                            'lessThanOrEqualTo' => '(<=) Less Than or Equal To',
                            'equalTo' => '(=) Equal To',
                            'contains' => 'Contains',
                            'doesNotContain' => 'Does Not Contain'
                            ),
                        'empty_value' => 'Choose a comparator'
                        ))
            ->add('value', null, array('attr' => array('class' => 'rankValue input-mini')));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'TSK\RulerBundle\Entity\Rule',
                'compound' => true,
            )
        );

        $resolver->setRequired(array());
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_rule';
    }
}
