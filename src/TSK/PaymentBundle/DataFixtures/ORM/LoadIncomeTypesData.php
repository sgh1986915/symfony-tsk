<?php
namespace TSK\PaymentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\IncomeType;

class LoadIncomeTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getIncomeTypes() as $incomeTypeName) {
            $incomeType = new IncomeType();
            $incomeType->setId($i++);
            $incomeType->setName($incomeTypeName);
            $incomeType->setOrganization($this->getReference('tsk-org'));
            $metadata = $manager->getClassMetaData(get_class($incomeType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->persist($incomeType);
            $manager->flush();
        }
    }

    public function getIncomeTypes()
    {
        $incomeTypes = array(
            'TUITION',
            'DEFERRED',
            'EQUIPMENT',
            'COLLECTIONS',
            'SEMINAR',
            'MISC',
            'PRIVATE',
            'TAX'
        );

        return $incomeTypes;
    }

    public function getOrder()
    {
        return 5;
    }
}
