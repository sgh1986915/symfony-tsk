<?php

namespace TSK\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Persistence\ObjectManager;
use TSK\UserBundle\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EntityHiddenType extends AbstractType
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @var ObjectManager
     */
    private $em;
    private $cache;

    /**
     * @param ObjectManager $om
     */
    public function __construct(EntityManager $em)
    {
        // $this->registry = $registry;
        $this->em = $em;
        $this->cache = array();
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // $resolver->setDefaults(array(
        //     'data_class' => null
        // ));
        $resolver->setRequired(array(
            'my_class'
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new EntityToIdTransformer($this->em, $options['my_class']);
        $builder->addViewTransformer($transformer);
        $builder->setAttribute('data_class', $options['my_class']);
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'entity_hidden';
    }

    protected function getClassFromMetadata($name, $parentClass)
    {
        /* @var $md \Doctrine\ORM\Mapping\ClassMetadata */
        $mds = $this->getMetadata($parentClass);
        $md  = $mds[0];
        $a = $md->getAssociationMapping($name);
        $class = $a['targetEntity'];

        return $class;
    }

    protected function getMetadata($class)
    {
        if (array_key_exists($class, $this->cache)) {
            return $this->cache[$class];
        }

        $this->cache[$class] = null;
        foreach ($this->registry->getManagers() as $name => $em) {
            try {
                return $this->cache[$class] = array($em->getClassMetadata($class), $name);
            } catch (MappingException $e) {
                // not an entity or mapped super class
            } catch (LegacyMappingException $e) {
                // not an entity or mapped super class, using Doctrine ORM 2.2
            }
        }
    }

}
