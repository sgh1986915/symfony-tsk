<?php
namespace TSK\PaymentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\PaymentMethod;

class LoadPaymentMethodsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getPaymentMethods() as $paymentMethodName => $payArray) {
            $paymentMethod = new PaymentMethod();
            $paymentMethod->setId($i++);
            $paymentMethod->setName($paymentMethodName);
            $paymentMethod->setPaymentType($payArray['type']);
            $paymentMethod->setIsRecurring($payArray['is_recurring']);
            $paymentMethod->setIsReceivable($payArray['is_receivable']);
            $paymentMethod->setAccount($payArray['account']);
            $paymentMethod->setOrganization($this->getReference('tsk-org'));
            $manager->persist($paymentMethod);
            $metadata = $manager->getClassMetaData(get_class($paymentMethod));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
        }
    }

    public function getPaymentMethods()
    {
        $paymentMethods = array(
            'CASH'  => array('type' => 'OTHER', 'is_recurring' => false, 'is_receivable' => true, 'account' => $this->getReference('Deposit Cash & Checks')),
            'CHECK' => array('type' => 'OTHER', 'is_recurring' => false, 'is_receivable' => true, 'account' => $this->getReference('Deposit Cash & Checks')),
            'ACH'   => array('type' => 'ACH', 'is_recurring' => true, 'is_receivable' => false, 'account' => $this->getReference('Deposit Cash & Checks')),
            'VISA'  => array('type' => 'CREDIT CARD', 'is_recurring' => true, 'is_receivable' => true, 'account' => $this->getReference('Deposit Credit Cards')),
            'MASTERCARD' => array('type' => 'CREDIT CARD', 'is_recurring' => true, 'is_receivable' => true, 'account' => $this->getReference('Deposit Credit Cards')),
            'AMERICAN EXPRESS' => array('type' => 'CREDIT CARD', 'is_recurring' => true, 'is_receivable' => true, 'account' => $this->getReference('Deposit Credit Cards')),
            'DISCOVER' => array('type' => 'CREDIT CARD', 'is_recurring' => true, 'is_receivable' => true, 'account' => $this->getReference('Deposit Credit Cards')),
            'GIFT CERTIFICATE' => array('type' => 'OTHER', 'is_recurring' => false, 'is_receivable' => true, 'account' => $this->getReference('Deposit Cash & Checks')),
            'CREDIT' => array('type' => 'OTHER', 'is_recurring' => false, 'is_receivable' => false, 'account' => $this->getReference('Deposit Cash & Checks'))
        );

        return $paymentMethods;
    }

    public function getOrder()
    {
        return 4;
    }
}
