<?php
namespace TSK\ProgramBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\DiscountType;

class LoadDiscountTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i = 1;
        foreach ($this->getDiscountTypes() as $planTypeName) {
            $planType = new DiscountType();
            $planType->setOrganization($this->getReference('tsk-org'));
            $planType->setId($i++);
            $planType->setName($planTypeName);
            $manager->persist($planType);
            $metadata = $manager->getClassMetaData(get_class($planType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
            $this->addReference('tsk_payment_plan_type-' . $planTypeName, $planType);
        }
    }

    public function getDiscountTypes()
    {
        $planTypes = array(
            'Regular',
            '2nd Family Member',
            '3rd Family Member',
            'Veteran'
        );
        return $planTypes;
    }

    public function getOrder()
    {
        return 5;
    }
}
