<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('firstName', 'text');
        $builder->add('lastName', 'text');
        $builder->add('email', 'text');
        $builder->add('organization', 'entity', array(
            'empty_value' => '-- Select',
            'class' => 'TSKUserBundle:Organization',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('o')
                    ->orderBy('o.title', 'ASC');
            }
        ));
    }

    public function getName()
    {
        return 'contact';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\UserBundle\Entity\Contact',
        ));
    }
}
