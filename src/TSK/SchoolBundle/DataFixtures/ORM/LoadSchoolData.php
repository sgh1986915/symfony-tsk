<?php
namespace TSK\SchoolBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\UserBundle\Entity\User;
use TSK\UserBundle\Entity\UserType;
use TSK\UserBundle\Entity\Organization;
use TSK\UserBundle\Entity\Contact;
use TSK\SchoolBundle\Entity\School;
use TSK\UserBundle\Entity\Corporation;

class LoadSchoolData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $corp = new Corporation();
        $corp->setLegalName('Elmwood LLC');
        $corp->setTaxId('239423423');
        $manager->persist($corp);
        $manager->flush();

        $schoolContact = new Contact();
        $schoolContact->setFirstName('Elmwood');
        $schoolContact->setLastName('Park');
        $schoolContact->setEmail('elmwood@tsk.com');
        $schoolContact->setOrganization($this->getReference('tsk-org'));
        $schoolContact->setAddress1('75 Boulevard');
        $schoolContact->setCity('Elmwood Park');
        $schoolContact->setState($manager->getRepository('TSK\UserBundle\Entity\States')->find('NJ'));
        $schoolContact->addCorporation($corp);
        $manager->persist($schoolContact);
        $manager->flush();
           
        $school = new School();
        $school->setName('Elmwood Park');
        $school->setContact($schoolContact);
        $school->setLatePaymentCharge(12.00);
        $school->setLateGraceDays(5);
        $manager->persist($school);
        $manager->flush();
        $this->addReference('tsk_elmwoodpark_school', $school);

        $soCorp = new Corporation();
        $soCorp->setLegalName('South Orange LLC');
        $soCorp->setTaxId('384823232');
        $manager->persist($soCorp);
        $manager->flush();

        $soContact = new Contact();
        $soContact->setFirstName('South');
        $soContact->setLastName('Orange');
        $soContact->setEmail('so@tsk.com');
        $soContact->setAddress1('76 South Orange Avenue');
        $soContact->setCity('South Orange');
        $soContact->setState($manager->getRepository('TSK\UserBundle\Entity\States')->find('NJ'));
        $soContact->setPostalCode('07079');
        $soContact->setOrganization($this->getReference('mjh-org'));
        $soContact->addCorporation($soCorp);

        $manager->persist($soContact);
        $manager->flush();
           
        $soSchool = new School();
        $soSchool->setName('South Orange');
        $soSchool->setContact($soContact);
        $soSchool->setLatePaymentCharge(12.00);
        $soSchool->setLateGraceDays(5);
        $this->addReference('mjh_southorange_school', $soSchool);
 
        $manager->persist($soSchool);
        $manager->flush();

        $corp = new Corporation();
        $corp->setLegalName('TS OF ROUTE 17 INC.');
        $corp->setDba("TIGER SCHULMANN'S MMA");
        $corp->setTaxId('45-5615200');
        $corp->setAbbrLegalName('RAMSEY');
        $corp->setRoutingNum('021200339');
        $corp->setAccountNum('123456789012');
        $manager->persist($corp);
        $manager->flush();

        $contactManager = $this->container->get('tsk_user.contact_manager');
        $schoolContact = $contactManager->createContact();
        $schoolContact->setFirstName('TSMMA');
        $schoolContact->setLastName('RAMSEY');
        $schoolContact->setEmail('ramsey@tsk.com');
        $schoolContact->setOrganization($this->getReference('tsk-org'));
        $schoolContact->setAddress1('885 State Highway 17 South');
        $schoolContact->setCity('Ramsey');
        $schoolContact->setState($manager->getRepository('TSK\UserBundle\Entity\States')->find('NJ'));
        $schoolContact->setPostalCode('07446');
        $schoolContact->setPhone('2016693566');
        $schoolContact->setMobile('2012369618');
        $schoolContact->setWebsite('http://ramseymma.com');
        $schoolContact->addCorporation($corp);
        $contactManager->updateCanonicalFields($schoolContact);
           
        $school = new School();
        $school->setName('Ramsey');
        $school->setContact($schoolContact);
        $school->setLatePaymentCharge(12.00);
        $school->setLateGraceDays(10);
        $school->setLegacyId('110');
        $manager->persist($school);
        $manager->flush();
        $this->addReference('tsk_ramsey_school', $school);


        // Add schools to contacts
        $mjhContact = $this->getReference('mjh-contact1');
        $mjhContact->addSchool($this->getReference('mjh_southorange_school'));
        $mjhContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($mjhContact);
        $manager->flush();

        $justinContact = $this->getReference('mjh-contact2');
        $justinContact->addSchool($this->getReference('mjh_southorange_school'));
        $manager->persist($justinContact);
        $manager->flush();

        $justinContact = $this->getReference('mjh-contact3');
        $justinContact->addSchool($this->getReference('mjh_southorange_school'));
        $manager->persist($justinContact);
        $manager->flush();


        $justinContact = $this->getReference('mjh-contact4');
        $justinContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $justinContact->addSchool($this->getReference('mjh_southorange_school'));
        $manager->persist($justinContact);
        $manager->flush();


        $tigerContact = $this->getReference('tsk-contact1');
        $tigerContact->addSchool($this->getReference('mjh_southorange_school'));
        $tigerContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($tigerContact);
        $manager->flush();

        $tContact = $this->getReference('tsk-contact2');
        $tContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($tContact);
        $manager->flush();

        $tContact = $this->getReference('tsk-contact3');
        $tContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $manager->persist($tContact);
        $manager->flush();

        $tContact = $this->getReference('tsk-contact4');
        $tContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $tContact->addSchool($this->getReference('mjh_southorange_school'));
        $manager->persist($tContact);

        $tContact = $this->getReference('tsk-contact5');
        $tContact->addSchool($this->getReference('tsk_elmwoodpark_school'));
        $tContact->addSchool($this->getReference('mjh_southorange_school'));
        $manager->persist($tContact);

    }

    public function getOrder()
    {
        return 6;
    }
}
