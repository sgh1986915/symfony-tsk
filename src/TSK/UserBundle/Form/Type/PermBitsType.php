<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

class PermBitsType extends AbstractType
{
    public function getName()
    {
        return 'tsk_permbits';
    }

    public function getParent()
    {
        return 'choice';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $permbits = array('list' => 'list', 'view' => 'view','create' => 'create','edit' => 'edit','delete' => 'delete','owner' => 'owner', 'export' => 'export');
        $resolver->setDefaults(array(
            'required' => false,
            'choices' => $permbits,
            'multiple' => true,
            // 'data_class' => 'TSK\UserBundle\Form\Model\PermBit',
            'expanded' => true,
        ));
    }
}
