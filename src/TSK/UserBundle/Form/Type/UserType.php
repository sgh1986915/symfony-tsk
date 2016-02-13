<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use TSK\UserBundle\Form\Type\ContactType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'text');
        $builder->add('password', 'text');
        $builder->add('user_type', 'choice', array('choices' => array('instructor' => "Instructor", 'owner' => 'Owner')));
        $builder->add('contact', new ContactType());
        $builder->add('is_admin', 'choice', array('choices' => array('0' => "Non-admin", '1' => 'Admin')));
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\UserBundle\Entity\User',
        ));
    }
}
