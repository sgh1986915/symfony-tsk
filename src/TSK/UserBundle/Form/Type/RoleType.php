<?php
namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use TSK\UserBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoleType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $roles = $this->em->createQuery('SELECT r FROM TSKUserBundle:Role r')->execute();
        foreach ($roles as $role) {
            $choices[$role->getName()] = $role->getName();
        }
        $resolver->setDefaults(array(
            'required' => true,
            'expanded' => true,
            'multiple' => true,
            'choices' => $choices
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'tsk_role_type';
    }
}
