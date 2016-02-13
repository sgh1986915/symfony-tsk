<?php
namespace TSK\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\UserBundle\Entity\Role;

class LoadRolesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $role = new Role();
        $role->setId(1);
        $role->setOrganization($this->getReference('tsk-org'));
        $role->setName('ROLE_USER');
        $manager->persist($role);
        $metadata = $manager->getClassMetaData(get_class($role));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-role_user', $role);

        $adminRole = new Role();
        $adminRole->setId(2);
        $adminRole->setOrganization($this->getReference('tsk-org'));
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);
        $metadata = $manager->getClassMetaData(get_class($adminRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-role_admin', $adminRole);

        $schoolRole = new Role();
        $schoolRole->setId(3);
        $schoolRole->setOrganization($this->getReference('tsk-org'));
        $schoolRole->setName('ROLE_SCHOOL_ADMIN');
        $schoolRole->setParentRoles(array($adminRole));
        $manager->persist($schoolRole);
        $metadata = $manager->getClassMetaData(get_class($schoolRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-role_school_admin', $schoolRole);

        $orgRole = new Role();
        $orgRole->setId(4);
        $orgRole->setOrganization($this->getReference('tsk-org'));
        $orgRole->setName('ROLE_ORG_ADMIN');
        $orgRole->setParentRoles(array($schoolRole));
        $manager->persist($orgRole);
        $metadata = $manager->getClassMetaData(get_class($orgRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-role_org_admin', $role);

        $role = new Role();
        $role->setId(5);
        $role->setOrganization($this->getReference('tsk-org'));
        $role->setName('ROLE_SUPER_ADMIN');
        $role->setParentRoles(array($orgRole));
        $manager->persist($role);
        $metadata = $manager->getClassMetaData(get_class($role));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk-role_super_admin', $role);

        $role = new Role();
        $role->setId(6);
        $role->setOrganization($this->getReference('mjh-org'));
        $role->setName('ROLE_USER');
        $manager->persist($role);
        $metadata = $manager->getClassMetaData(get_class($role));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-role_user', $role);

        $adminRole = new Role();
        $adminRole->setId(7);
        $adminRole->setOrganization($this->getReference('mjh-org'));
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($role);
        $metadata = $manager->getClassMetaData(get_class($adminRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-role_admin', $adminRole);

        $schoolRole = new Role();
        $schoolRole->setId(8);
        $schoolRole->setOrganization($this->getReference('mjh-org'));
        $schoolRole->setName('ROLE_SCHOOL_ADMIN');
        $orgRole->setParentRoles(array($adminRole));
        $manager->persist($schoolRole);
        $metadata = $manager->getClassMetaData(get_class($schoolRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-role_school_admin', $schoolRole);

        $orgRole = new Role();
        $orgRole->setId(9);
        $orgRole->setOrganization($this->getReference('mjh-org'));
        $orgRole->setName('ROLE_ORG_ADMIN');
        $orgRole->setParentRoles(array($schoolRole));
        $manager->persist($orgRole);
        $metadata = $manager->getClassMetaData(get_class($orgRole));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-role_org_admin', $orgRole);

        $role = new Role();
        $role->setId(10);
        $role->setOrganization($this->getReference('mjh-org'));
        $role->setName('ROLE_SUPER_ADMIN');
        $role->setParentRoles(array($orgRole));
        $manager->persist($role);
        $metadata = $manager->getClassMetaData(get_class($role));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('mjh-role_super_admin', $role);
    }

    public function getOrder()
    {
        return 0.1;
    }
}
