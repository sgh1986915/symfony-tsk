<?php
namespace TSK\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use TSK\UserBundle\Entity\User;
use TSK\UserBundle\Entity\UserType;
use TSK\UserBundle\Entity\States as State;
use TSK\UserBundle\Entity\Contact;

class LoadStatesData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $states = array(
"AL" => "Alabama",
"AK" => "Alaska",
"AS" => "American Samoa",
"AZ" => "Arizona",
"AR" => "Arkansas",
"CA" => "California",
"CO" => "Colorado",
"CT" => "Connecticut",
"DE" => "Delaware",
"DC" => "District of Columbia",
"FM" => "Federated States of Micronesia",
"FL" => "Florida",
"GA" => "Georgia",
"GU" => "Guam",
"HI" => "Hawaii",
"ID" => "Idaho",
"IL" => "Illinois",
"IN" => "Indiana",
"IA" => "Iowa",
"KS" => "Kansas",
"KY" => "Kentucky",
"LA" => "Louisiana",
"ME" => "Maine",
"MH" => "Marshall Islands",
"MD" => "Maryland",
"MA" => "Massachusetts",
"MI" => "Michigan",
"MN" => "Minnesota",
"MS" => "Mississippi",
"MO" => "Missouri",
"MT" => "Montana",
"NE" => "Nebraska",
"NV" => "Nevada",
"NH" => "New Hampshire",
"NJ" => "New Jersey",
"NM" => "New Mexico",
"NY" => "New York",
"NC" => "North Carolina",
"ND" => "North Dakota",
"MP" => "Northern Mariana Islands",
"OH" => "Ohio",
"OK" => "Oklahoma",
"OR" => "Oregon",
"PW" => "Palau",
"PA" => "Pennsylvania",
"PR" => "Puerto Rico",
"RI" => "Rhode Island",
"SC" => "South Carolina",
"SD" => "South Dakota",
"TN" => "Tennessee",
"TX" => "Texas",
"UT" => "Utah",
"VT" => "Vermont",
"VI" => "Virgin Islands",
"VA" => "Virginia",
"WA" => "Washington",
"WV" => "West Virginia",
"WI" => "Wisconsin",
"WY" => "Wyoming");
        foreach ($states as $abbr => $name) {
            $state = new State();
            $state->setId($abbr);
            $state->setStateName($name);
            $manager->persist($state);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 1;
    }
}
