<?php
namespace TSK\StudentBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\StudentBundle\Entity\StudentStatus;

class LoadStudentStatusData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $statuses = array(  'active' => 'Actively training with contract in good standing', 
                            'inactive' => 'Active contract in good standing but no attendance in X days', 
                            'cancelled' => 'Not training, contract obligations met or forgiven', 
                            'collections' => 'Contract in collections.  Not in good standing.', 
                            'expired' => 'Contract obligations fulfilled by both parties, no current ative contract exists.');
 
        $i=1;
        foreach ($statuses as $statusName => $statusDesc) {
            $studentStatus = new StudentStatus();
            $studentStatus->setId($i++);
            $studentStatus->setName($statusName);
            $studentStatus->setDescription($statusDesc);
            $studentStatus->setOrganization($this->getReference('tsk-org'));
            $manager->persist($studentStatus);
            $metadata = $manager->getClassMetaData(get_class($studentStatus));
            $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
            $manager->flush();
            $this->addReference('tsk-student_status-' . $statusName, $studentStatus);
        }
    }

    public function getOrder()
    {
        return 7;
    }
}
