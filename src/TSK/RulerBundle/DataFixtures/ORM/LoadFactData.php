<?php
namespace TSK\RulerBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\RulerBundle\Entity\Fact;

use Keboola\Csv\CsvFile;

class LoadFactData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getFacts() as $idx => $fct) {
            $fact = new Fact();
            $fact->setId($fct[0]);
            $fact->setOrganization($this->getReference('tsk-org'));
            $fact->setName($fct[2]);
            $fact->setDescription($fct[3]);
            $fact->setMethod($fct[4]);
            $fact->setFactType($fct[5]);
            $manager->persist($fact);
            $metadata = $manager->getClassMetaData(get_class($fact));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getFacts()
    {
        $ranks = array();
        $factCsvFile = $this->container->getParameter('tsk_rank.imports.fact_file');
        $csvFile = new CsvFile($factCsvFile);
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $facts[] = $row;
            }
        }
        return $facts;
    }

    public function getOrder()
    {
        return 5;
    }
}
