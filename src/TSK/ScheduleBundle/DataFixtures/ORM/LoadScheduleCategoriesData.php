<?php
namespace TSK\ScheduleBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use TSK\ScheduleBundle\Entity\ScheduleCategory;

class LoadScheduleCategoriesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getScheduleCategories() as $cat) {
            $sc = new ScheduleCategory();
            $sc->setOrganization($this->getReference('tsk-org'));
            $sc->setName($cat['name']);
            $sc->setColor($cat['color']);
            $manager->persist($sc);
            $this->addReference('schedule_category_' . $cat['name'], $sc);
        }
        $manager->flush();
    }

    public function getScheduleCategories()
    {
        $categories[] = array('name' => 'class', 'color' => '#cc0000');
        $categories[] = array('name' => 'appointment', 'color' => '#00cc00');
        return $categories;
    }

    public function getOrder()
    {
        return 8;
    }
}
