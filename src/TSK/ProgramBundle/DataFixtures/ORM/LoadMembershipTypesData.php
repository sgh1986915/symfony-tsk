<?php
namespace TSK\ProgramBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ProgramBundle\Entity\MembershipType;

class LoadMembershipTypesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $i=1;
        foreach ($this->getMembershipTypes() as $membershipTypeName) {
            $membershipType = new MembershipType();
            $membershipType->setId($i++);
            $membershipType->setOrganization($this->getReference('tsk-org'));
            $membershipType->setName($membershipTypeName);
            $manager->persist($membershipType);
            $metadata = $manager->getClassMetaData(get_class($membershipType));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();

            $manager->flush();
            $this->addReference('tsk-membership_type-'. $membershipTypeName, $membershipType);
        }
    }

    public function getMembershipTypes()
    {
        $membershipTypes = array(
            'martial arts',
            'martial arts w/ gym',
            'gym only',
            'cubs',
            'hq training',
            'seminar',
            'trial'
        );

        return $membershipTypes;
    }

    public function getOrder()
    {
        return 4;
    }
}
