<?php
namespace TSK\ClassBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ClassBundle\Entity\Classes;
use Keboola\Csv\CsvFile;

class LoadClassData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getClasses() as $c) {
            $class = new Classes();
            $class->setId($c[0]);
            $class->setOrganization($this->getReference('tsk-org'));
            $class->setTitle($c[2]);
            $class->setDescription($c[3]);
            $class->setTokens(1);
            $class->setIsActive($c[4]);
            $class->setScheduleColor('#' . $c[5]);
            $metadata = $manager->getClassMetaData(get_class($class));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->persist($class);
            $manager->flush();
        }
    }

    public function getClasses()
    {
        $classes = array();
        $classCsvFile = $this->container->getParameter('tsk_class.imports.class_file');
        $csvFile = new CsvFile($classCsvFile);
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $classes[] = $row;
            }
        }
        return $classes;
    }

    public function getOrder()
    {
        return 2;
    }
}
