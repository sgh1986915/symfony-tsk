<?php
namespace TSK\RulerBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\RulerBundle\Entity\RuleContext;

use Keboola\Csv\CsvFile;

class LoadRuleContextData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->getRuleContexts() as $idx => $rc) {
            $ruleContext = new RuleContext();
            $ruleContext->setName($rc['name']);
            $manager->persist($ruleContext);
        }
        $manager->flush();
    }

    public function getRuleContexts()
    {
        $ruleContexts = array();
        $ruleContexts[] = array('name' => 'discount.pre');
        $ruleContexts[] = array('name' => 'program.post');
        $ruleContexts[] = array('name' => 'program.pre');
        $ruleContexts[] = array('name' => 'program');
        $ruleContexts[] = array('name' => 'rank');
        return $ruleContexts;
    }

    public function getOrder()
    {
        return 5;
    }
}
