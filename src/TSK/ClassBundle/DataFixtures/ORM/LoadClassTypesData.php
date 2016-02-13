<?php
namespace TSK\ClassBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ClassBundle\Entity\ClassType;

class LoadClassTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getClassTypes() as $classTypeName) {
            $classType = new ClassType();
            $classType->setId($i++);
            $classType->setOrganization($this->getReference('tsk-org'));
            $classType->setName($classTypeName);
            $manager->persist($classType);
            $metadata = $manager->getClassMetaData(get_class($classType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getClassTypes()
    {
        $classTypes = array(
            'KICKBOXING',
            'GRAPPLING',
            'CORE',
            'CUBS',
            'OPEN'
        );

        return $classTypes;
    }

    public function getOrder()
    {
        return 3;
    }
}
