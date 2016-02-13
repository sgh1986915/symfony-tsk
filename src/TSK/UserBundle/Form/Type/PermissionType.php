<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as DoctrineRegistry;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\AdminBundle\Security\Acl\Permission\MaskBuilder;
use Sonata\AdminBundle\Admin\Pool;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use TSK\UserBundle\Form\Model\PermissionSet;
use TSK\UserBundle\Form\Model\Permission;
use TSK\UserBundle\Form\Model\PermBit;

class PermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $permbits = array('list' => 'list', 'view' => 'view','create' => 'create','edit' => 'edit','delete' => 'delete','owner' => 'owner', 'export' => 'export');
        $builder->add('className', NULL, array('required' => true));
        $builder->add('classType', NULL, array('required' => true));
        $builder->add('fieldName', NULL, array('required' => false));
        $builder->add('bits', 'choice', array(
            'multiple' => true,
            'expanded' => true,
            'choices' => $permbits,
            'attr'     => array(
                'class' => 'permbits'
            )
        ));

    }

    public function getName()
    {
        return 'tsk_permission_type';
    }

    public function getParent()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\UserBundle\Form\Model\Permission',
            'compound' => true
        ));
    }
}
