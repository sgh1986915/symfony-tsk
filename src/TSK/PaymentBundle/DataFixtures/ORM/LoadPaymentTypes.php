<?php
namespace TSK\PaymentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\PaymentType;

class LoadPaymentTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getPaymentTypes() as $paymentTypeName) {
            $paymentType = new paymentType();
            $paymentType->setId($i++);
            $paymentType->setName($paymentTypeName);
            $paymentType->setOrganization($this->getReference('tsk-org'));
            $manager->persist($paymentType);
            $metadata = $manager->getClassMetaData(get_class($paymentType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getPaymentTypes()
    {
        $paymentTypes = array(
            'payment',
            'credit',
            'refund'
        );

        return $paymentTypes;
    }

    public function getOrder()
    {
        return 5;
    }
}
