<?php
namespace TSK\PaymentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\PaymentBundle\Entity\Account;

class LoadAccountData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getAccounts() as $acc) {
            $account = new Account();
            $account->setId($i++);
            $account->setName($acc['name']);
            $account->setType($acc['type']);
            $account->setOrganization($this->getReference('tsk-org'));
            $manager->persist($account);
            $metadata = $manager->getClassMetaData(get_class($account));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
            $this->addReference($acc['name'], $account);
        }
    }

    public function getAccounts()
    {
        $accounts = array();
        $accounts[] = array('name' => 'Deposit Cash & Checks', 'type' => 'Asset/Bank');
        $accounts[] = array('name' => 'Deposit Credit Cards', 'type' => 'Asset/Bank');
        $accounts[] = array('name' => 'Carry-Forward', 'type' => 'Asset/Bank');
        $accounts[] = array('name' => 'Deferred Income', 'type' => 'Other Current Liability');
        $accounts[] = array('name' => 'Inc Fm Students', 'type' => 'Income');
        $accounts[] = array('name' => 'Discounts', 'type' => 'Expense');
        $accounts[] = array('name' => 'Promotion', 'type' => 'Expense');
        $accounts[] = array('name' => 'Write off', 'type' => 'Expense');

        return $accounts;
    }

    public function getOrder()
    {
        return 3;
    }
}
