<?php
namespace TSK\RankBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ContractBundle\Entity\ContractClause;

use Keboola\Csv\CsvFile;

class LoadContractClauseData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getClauses() as $idx => $clause) {
            $contractClause = new ContractClause();
            $contractClause->setId($clause[0]);
            $contractClause->setOrganization($this->getReference('tsk-org'));
            $contractClause->setName($clause[2]);
            $contractClause->setContent($clause[3]);
            $manager->persist($contractClause);
            $metadata = $manager->getClassMetaData(get_class($contractClause));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getClauses()
    {
        $clauses = array();
        $clauseCsvFile = $this->container->getParameter('tsk_contract.imports.clause_file');
        $csvFile = new CsvFile($clauseCsvFile, ';');
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $clauses[] = $row;
            }
        }
        return $clauses;
    }

    public function getOrder()
    {
        return 8;
    }
}
