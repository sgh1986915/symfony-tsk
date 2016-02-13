<?php
namespace TSK\RankBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\RankBundle\Entity\RankType;

use Keboola\Csv\CsvFile;

class LoadRankTypeData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getRankTypes() as $idx => $rnk) {
            $rt = new RankType();
            $rt->setId($idx);
            $rt->setOrganization($this->getReference('tsk-org'));
            $rt->setName($rnk[0]);
            $manager->persist($rt);
            $metadata = $manager->getClassMetaData(get_class($rt));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
            $this->addReference('rank_type-' . $rnk[0], $rt);
        }
    }

    public function getRankTypes()
    {
        $rankTypes = array();
        $rankTypes[1] = array('belt');
        $rankTypes[2] = array('kickboxing stripe');
        $rankTypes[3] = array('grappling stripe');
        return $rankTypes;
    }

    public function getOrder()
    {
        return 3;
    }
}
