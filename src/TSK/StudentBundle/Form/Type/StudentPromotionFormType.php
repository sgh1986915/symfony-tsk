<?php

namespace TSK\StudentBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

class StudentPromotionFormType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\StudentBundle\Entity\StudentRank',
            'compound' => true,
            'error_bubbling' => true
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('rank', 'entity_hidden', array(
            'my_class' => 'TSK\RankBundle\Entity\Rank'
        ));
        $builder->add('student', 'entity_hidden', array(
            'my_class' => 'TSK\StudentBundle\Entity\Student'
        ));
        $builder->add('rankType', 'entity_hidden', array(
            'my_class' => 'TSK\RankBundle\Entity\RankType'
        ));
    }

    public function getParent()
    {
        return 'form';
    }

    public function getName()
    {
        return 'tsk_student_promotion_form_type';
    }
}
