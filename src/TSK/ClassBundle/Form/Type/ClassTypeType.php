<?php
namespace TSK\ClassBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use TSK\ClassBundle\Form\DataTransformer\ClassTypeToNumberTransformer;
use TSK\ClassBundle\Form\EventListener\PopulateMetaFields;

class ClassTypeType extends AbstractType
{
    private $em;
    private $session;
    private $sessionKey;
    public function __construct($em, $session, $sessionKey)
    {
        $this->em = $em;
        $this->session = $session;
        $this->sessionKey = $sessionKey;
    }

    /**
     * buildForm 
     * Grabs organization-specific class types
     * 
     * @param FormBuilderInterface $builder 
     * @param array $options 
     * @access public
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $org = $this->session->get($this->sessionKey);
        // $builder->add('class_type', 'entity', array(
        //                 'property' => 'name',
        //                 'label' => 'Class Type',
        //                 'attr' => array('class' => 'input-medium'),
        //                 'class' => 'TSKClassBundle:ClassType', 
        //                 'query_builder' => function(EntityRepository $er) use ($org) {
        //                         return $er->createQueryBuilder('u')
        //                             ->where('u.organization=:org')
        //                             ->setParameter('org', $org);
        //                 }
        // ));
        $builder->add('class_type', 'entity_hidden', array(
            'my_class' => 'TSKClassBundle:ClassType'
        ));
        $builder->add('value', NULL, array('attr' => array('class' => 'input-mini')));
    }

    public function getName()
    {
        return 'tsk_class_types';
    }
    
    public function getParent()
    {
        return 'form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'TSK\ClassBundle\Entity\ClassTypeCredit'
        ));
    }

}
