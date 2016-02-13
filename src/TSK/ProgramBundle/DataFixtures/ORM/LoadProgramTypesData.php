<?php
namespace TSK\ProgramBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ProgramBundle\Entity\ProgramType;

class LoadProgramTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getProgramTypes() as $programTypeName) {
            $programType = new ProgramType();
            $programType->setId($i++);
            $programType->setOrganization($this->getReference('tsk-org'));
            $programType->setName($programTypeName);
            $manager->persist($programType);
            $metadata = $manager->getClassMetaData(get_class($programType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
            $this->addReference('tsk-program_type-'. $programTypeName, $programType);
        }
    }

    public function getProgramTypes()
    {
        $programTypes = array(
            'TIME',
            'TOKEN'
        );

        return $programTypes;
    }

    public function getOrder()
    {
        return 4;
    }
}
