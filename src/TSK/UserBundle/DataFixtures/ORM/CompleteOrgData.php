<?php
namespace TSK\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\UserBundle\Entity\User;
use TSK\UserBundle\Entity\UserType;
use TSK\UserBundle\Entity\Organization;
use TSK\UserBundle\Entity\Contact;
use TSK\UserBundle\Entity\Corporation;

class CompleteOrgData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
/*
        $org = $this->getReference('tsk-org');
        $c = new Contact();
        $c->setFirstName('TSK Org');
        $c->setLastName('TSK Org');
        $c->setEmail('tsk_org@tsk.com');
        $c->setOrganization($org);

        $corp = new Corporation();
        $corp->setTaxId('8675309');
        $corp->setAccountNum('283428423');
        $corp->setRoutingNum('777777777');
        $corp->setLegalName('Tiger Schulmann Mixed Martial Arts');
        $corp->setAbbrLegalName('TSMMA');
        $corp->setDba('Tiger Schulmann Mixed Martial Arts Center');

        $c->addCorporation($corp);

        $manager->persist($c);
        $manager->flush();

        $org->addContact($c);
        $manager->persist($org);
        $manager->flush();
 
        $org = $this->getReference('mjh-org');
        $c = new Contact();
        $c->setFirstName('MJH Org');
        $c->setLastName('MJH Org');
        $c->setEmail('mjh_org@mjh.com');
        $c->setOrganization($org);

        $corp = new Corporation();
        $corp->setTaxId('28342732');
        $corp->setAccountNum('238422323');
        $corp->setRoutingNum('2222222222');
        $corp->setLegalName('Malaney Justin Hill Tech Fu');
        $corp->setAbbrLegalName('MJHTF');
        $corp->setDba('Malaney J Hill Tech Fu Centers');

        $c->addCorporation($corp);


        $manager->persist($c);
        $manager->flush();

        $org->addContact($c);
        $manager->persist($org);
        $manager->flush();
*/
    }

    public function getOrder()
    {
        return 9;
    }
}
