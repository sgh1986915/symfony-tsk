<?php
namespace TSK\RankBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ContractBundle\Entity\ContractStatus;


class LoadContractStatusesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getStatuses() as $idx => $status) {
            $contractStatus = new ContractStatus();
            $contractStatus->setId($idx + 1);
            $contractStatus->setOrganization($this->getReference('tsk-org'));
            $contractStatus->setName($status);
            $manager->persist($contractStatus);
            $metadata = $manager->getClassMetaData(get_class($contractStatus));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getStatuses()
    {
        $statuses = array('initial', 'cancelled', 'expired', 'collections', 'renewed', 'revised', 'time added', 'classes added');
        return $statuses;
    }

    public function getOrder()
    {
        return 8;
    }
}
