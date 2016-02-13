<?php
namespace TSK\PaymentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\PaymentTerm;

class LoadPaymentTermsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getPaymentTerms() as $paymentTermName) {
            $paymentTerm = new PaymentTerm();
            $paymentTerm->setId($i++);
            $paymentTerm->setName($paymentTermName);
            $paymentTerm->setOrganization($this->getReference('tsk-org'));
            $manager->persist($paymentTerm);
            $metadata = $manager->getClassMetaData(get_class($paymentTerm));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getPaymentTerms()
    {
        $paymentTerms = array(
            'PAID IN FULL',
            'MONTHLY',
            'WEEKLY',
            'BIWEEKLY'
        );

        return $paymentTerms;
    }

    public function getOrder()
    {
        return 3;
    }
}
