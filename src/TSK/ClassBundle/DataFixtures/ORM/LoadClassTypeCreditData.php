<?php
namespace TSK\ClassBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ClassBundle\Entity\ClassTypeCredit;
use Keboola\Csv\CsvFile;

class LoadClassTypeCreditData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        foreach ($this->getClasses() as $c) {
            $ctc = new ClassTypeCredit();
            $ctc->setClass($em->getRepository('TSK\ClassBundle\Entity\Classes')->find($c[1]));
            $ctc->setClassType($em->getRepository('TSK\ClassBundle\Entity\ClassType')->find($c[2]));
            $ctc->setValue($c[3]);
            $manager->persist($ctc);
        }
        $manager->flush();
    }

    public function getClasses()
    {
        $classes = array();
        $classCsvFile = $this->container->getParameter('tsk_class.imports.class_type_credit_file');
        $csvFile = new CsvFile($classCsvFile);
        foreach($csvFile as $row) {
            if (is_numeric($row[0])) {
                $classes[] = $row;
            }
        }
        return $classes;
    }

    public function getOrder()
    {
        return 4;
    }
}
