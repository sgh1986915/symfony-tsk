<?php
namespace TSK\RankBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\RankBundle\Entity\Rank;

use Keboola\Csv\CsvFile;

class LoadRankData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $rts = array(1 => 'belt', 2 =>  'kickboxing stripe', 3 => 'grappling stripe');
        foreach ($this->getRanks() as $idx => $rnk) {
            $rank = new Rank();
            $rank->setId($rnk[0]);
            $rank->setOrganization($this->getReference('tsk-org'));
            $rank->setName($rnk[2]);
            $rank->setRankOrder($rnk[3]);
            $rank->setRankType($this->getReference('rank_type-' . $rts[$rnk[4]]));// only for now
            $rank->setKbStripe($rnk[5]);
            $rank->setGrStripe($rnk[6]);
            $rank->setDescription($rnk[7]);
            $rank->setFullDescription($rnk[8]);
            $manager->persist($rank);
            $metadata = $manager->getClassMetaData(get_class($rank));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getRanks()
    {
        $ranks = array();
        $rankCsvFile = $this->container->getParameter('tsk_rank.imports.rank_file');
        $csvFile = new CsvFile($rankCsvFile);
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $ranks[] = $row;
            }
        }
        return $ranks;
    }

    public function getOrder()
    {
        return 4;
    }
}
