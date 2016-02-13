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

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
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

        $c = new Contact();
        $c->setFirstName('Eli');
        $c->setLastName('Khoury');
        $c->setEmail('eli@tsk.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('tsk-contact3', $c);

        $c = new Contact();
        $c->setFirstName('Doug');
        $c->setLastName('Parody');
        $c->setEmail('dparody@tsk.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('tsk-contact4', $c);

        $c = new Contact();
        $c->setFirstName('TSK Org');
        $c->setLastName('Admin');
        $c->setEmail('tskorg@tsk.com');
        $c->setOrganization($this->getReference('tsk-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('tsk-contact5', $c);

        $userManager = $this->container->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('tsk_school_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('tsk-contact1'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_SCHOOL_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('tsk_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('tsk-contact2'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('eli');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('tsk-contact3'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_SUPER_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('doug');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('tsk-contact4'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_SUPER_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('tsk_org_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('tsk-contact5'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_ORG_ADMIN');
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
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_SUPER_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();
        $this->addReference('mjh-user', $user);

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
        $user->setUsername('mjh_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('mjh-contact2'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $c = new Contact();
        $c->setFirstName('School Admin');
        $c->setLastName('Hill');
        $c->setEmail('schooladmin@gmail.com');
        $c->setOrganization($this->getReference('mjh-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('mjh-contact3', $c);


        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('mjh_school_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('mjh-contact3'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_SCHOOL_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();

        $c = new Contact();
        $c->setFirstName('Org Admin');
        $c->setLastName('Hill');
        $c->setEmail('orgadmin@gmail.com');
        $c->setOrganization($this->getReference('mjh-org'));
        $manager->persist($c);
        $manager->flush();
        $this->addReference('mjh-contact4', $c);

        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setUsername('mjh_org_admin');
        $user->setPlainPassword('password1');
        $user->setContact($this->getReference('mjh-contact4'));
        $user->addRole('ROLE_USER');
        $user->addRole('ROLE_ADMIN');
        $user->addRole('ROLE_ORG_ADMIN');
        $userManager->updateUser($user);

        $manager->persist($user);
        $manager->flush();
    }

    public function getOrder()
    {
        return 1.5;
    }
}
