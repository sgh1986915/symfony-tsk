<?php
namespace TSK\ProgramBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\ProgramBundle\Entity\Program;
use TSK\ProgramBundle\Entity\ProgramPaymentPlan;

class LoadProgramData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $ts = new \DateTime('2017-01-01');
        $program = new Program();
        $program->setId(1);
        $program->setOrganization($this->getReference('tsk-org'));
        $program->setExpirationDate($ts->getTimestamp());
        $program->setIsActive(true);
        $program->setDescription('12 Month Unlimited');
        $program->setProgramName('12 Month Unlimited');
        $program->setProgramType($this->getReference('tsk-program_type-TIME'));
        $program->setLegalDescription('12 Month Unlimited');
        $program->setDiscountType($this->getReference('tsk_payment_plan_type-Regular'));
        $program->setMembershipType($this->getReference('tsk-membership_type-martial arts'));
        $program->setDurationDays(365);
        $program->addSchool($this->getReference('mjh_southorange_school'));
        $program->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($program);
        $metadata = $manager->getClassMetaData(get_class($program));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk_program_12_month', $program);

        $programPaymentPlan = new ProgramPaymentPlan();
        $programPaymentPlan->setName('Monthly');
        $programPaymentPlan->setIsActive(true);
        $programPaymentPlan->setProgram($this->getReference('tsk_program_12_month'));
        $foo = array(
            'principal' => '1200', 
            'paymentFrequency' => 'monthly',
            'payments' => array(120,120,120,120,120,120,120,120,120,120),
            'summary' => '10 payments totalling $1200 (10 @ $120)'
        );
        $programPaymentPlan->setPaymentsData(array('paymentsData' => json_encode($foo)));
        $programPaymentPlan->setDeferralRate(0.75);
        $programPaymentPlan->setDeferralDurationMonths(10);
        $programPaymentPlan->setDeferralDistributionStrategy('straight');
        $manager->persist($programPaymentPlan);
        $manager->flush();

        $programPaymentPlan = new ProgramPaymentPlan();
        $programPaymentPlan->setName('6 Month Payoff');
        $programPaymentPlan->setIsActive(true);
        $programPaymentPlan->setProgram($this->getReference('tsk_program_12_month'));
        $foo = array(
            'principal' => '1100', 
            'paymentFrequency' => 'monthly',
            'payments' => array(200,180,180,180,180,180),
            'summary' => '1 payment of 200 followed by 5 payments of 180 totalling $1100 (1 @ $200, 5 @ $180)'
        );
        $programPaymentPlan->setPaymentsData(array('paymentsData' => json_encode($foo)));
        $programPaymentPlan->setDeferralRate(0.75);
        $programPaymentPlan->setDeferralDurationMonths(10);
        $programPaymentPlan->setDeferralDistributionStrategy('straight');

        $manager->persist($programPaymentPlan);
        $manager->flush();

        $program = new Program();
        $program->setId(2);
        $program->setOrganization($this->getReference('tsk-org'));
        $program->setExpirationDate($ts->getTimestamp());
        $program->setIsActive(true);
        $program->setDescription('36 Month Unlimited');
        $program->setProgramName('36 Month Unlimited');
        $program->setProgramType($this->getReference('tsk-program_type-TIME'));
        $program->setLegalDescription('12 Month Unlimited');
        $program->setDiscountType($this->getReference('tsk_payment_plan_type-Regular'));
        $program->setMembershipType($this->getReference('tsk-membership_type-martial arts'));
        $program->setDurationDays(1095);
        $program->addSchool($this->getReference('mjh_southorange_school'));
        $program->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($program);
        $metadata = $manager->getClassMetaData(get_class($program));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $manager->flush();
        $this->addReference('tsk_program_36_month', $program);

        $programPaymentPlan = new ProgramPaymentPlan();
        $programPaymentPlan->setName('Regular');
        $programPaymentPlan->setIsActive(true);
        $programPaymentPlan->setProgram($program);
        $foo = array(
            'principal' => '3000', 
            'paymentFrequency' => 'monthly',
            'payments' => array(500,500,500,500,500,500),
            'summary' => '6 payments totalling $3000 (6 @ $500)'
        );
        $programPaymentPlan->setPaymentsData(array('paymentsData' => json_encode($foo)));
        $programPaymentPlan->setDeferralRate(0.75);
        $programPaymentPlan->setDeferralDurationMonths(24);
        $programPaymentPlan->setDeferralDistributionStrategy('straight');
        $manager->persist($programPaymentPlan);
        $manager->flush();
    }

    public function getOrder()
    {
        return 7;
    }
}
