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

class LoadOrgData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $org = new Organization();
        $org->setId(1);
        $org->setTitle('TSK');
        $manager->persist($org);
        $metadata = $manager->getClassMetaData(get_class($org));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-org', $org);

        $org = new Organization();
        $org->setId(2);
        $org->setTitle('MJH');
        $manager->persist($org);
        $metadata = $manager->getClassMetaData(get_class($org));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-org', $org);

    }
/*
 
        $c = new Contact();
        $c->setFirstName('Tiger');
        $c->setLastName('Schulmann');
        $c->setEmail('tsk@tsk.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('tsk-contact1', $c);
           
        $c = new Contact();
        $c->setFirstName('Tiny');
        $c->setLastName('Schulmann');
        $c->setEmail('tsk2@tsk.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('tsk-contact2', $c);
 
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('tigeradmin');
        $user->setPlainPassword('scott');
        $user->setContact($this->getReference('tsk-contact1'));
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('tigeruser');
        $user->setPlainPassword('scotty');
        $user->setContact($this->getReference('tsk-contact2'));
        $user->addRole('ROLE_USER');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        ///////////////////////////////////////////////////////////
 
        $c = new Contact();
        $c->setFirstName('Malaney');
        $c->setLastName('Hill');
        $c->setEmail('malaney@gmail.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('mjh-contact1', $c);
           
        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('mhill');
        $user->setPlainPassword('test');
        $user->setContact($this->getReference('mjh-contact1'));
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $c = new Contact();
        $c->setFirstName('Justin');
        $c->setLastName('Hill');
        $c->setEmail('malaney@gmail.com');
        $c->setOrganization($this->getReference('mjh-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('mjh-contact2', $c);


        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('mhilluser');
        $user->setPlainPassword('test2');
        $user->setContact($this->getReference('mjh-contact2'));
        $user->addRole('ROLE_USER');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

    }
*/

    public function getOrder()
    {
        return 0;
    }
}
