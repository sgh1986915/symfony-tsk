<?php
namespace TSK\ScheduleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use TSK\ScheduleBundle\Entity\ScheduleLocation;

class LoadScheduleLocationsData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getScheduleLocations() as $loc) {
            $sl = new ScheduleLocation();
            $sl->setOrganization($this->getReference('tsk-org'));
            $sl->setName($loc['name']);
            $manager->persist($sl);
            $this->addReference('schedule_location_'.$loc['name'], $sl);
        }
        $manager->flush();
    }

    public function getScheduleLocations()
    {
        $locations[] = array('name' => 'Mat 1');
        $locations[] = array('name' => 'Mat 2');
        return $locations;
    }

    public function getOrder()
    {
        return 8;
    }
}
