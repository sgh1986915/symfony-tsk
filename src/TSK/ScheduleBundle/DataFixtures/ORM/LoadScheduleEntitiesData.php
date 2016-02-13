<?php
namespace TSK\ScheduleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use TSK\ScheduleBundle\Entity\ScheduleCategory;
use TSK\ScheduleBundle\Entity\ScheduleEntity;

class LoadScheduleEntitiesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $today = new \DateTime();
        $se = new ScheduleEntity();
        $se->setUser($this->getReference('mjh-user'));;
        $se->setSchool($this->getReference('tsk_ramsey_school'));;
        $se->setCategory($this->getReference('schedule_category_class'));
        $se->setLocation($this->getReference('schedule_location_Mat 1'));;
        $se->setTitle('Dummy Entity');
        $se->setStartAt($today);
        $se->setEndAt($today->add(new \DateInterval('PT1H')));
        $se->setCreatedAt($today);
        $se->setLastModifiedAt($today);
        $se->setOptions(array());
        $manager->persist($se);
        $manager->flush();
    }

    public function getOrder()
    {
        return 9;
    }
}
