<?php

namespace TSK\RankBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RankValueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'text');
    }

    public function getName()
    {
        return 'tsk_rank_value_type';
    }
}
