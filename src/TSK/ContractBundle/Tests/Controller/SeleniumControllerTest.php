<?php
namespace TSK\ContractBundle\Tests\Controller;

class SeleniumControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $today;
    protected $yesterday;
    protected $tomorrow;
    protected function setUp()
    {
        $this->setBrowser('firefox');
        $this->setBrowserUrl('http://bix.dev/');

        $this->today = new \DateTime();
        $this->yesterday = new \DateTime('yesterday');
        $this->tomorrow = new \DateTime('tomorrow');
    }

    public function xtestFreeze()
    {
        $this->login();

        $this->open('/app_dev.php/admin/contract/freeze/1');
        $this->assertContains('Start Date', $this->getHtmlSource());

        // Check startdate not in the past
        $this->type("id=tsk_contract_freeze_startDate", $this->yesterday->format('Y-m-d'));
        $this->type("id=tsk_contract_freeze_endDate", $this->tomorrow->format('Y-m-d'));
        $this->click("css=input[type=\"submit\"]");
        $this->waitForPageToLoad("30000");
        $this->assertNotContains('Contract freeze saved', $this->getHtmlSource());

        // Check startdate > enddate
        $tenDays = clone $this->tomorrow;
        $tenDays->add(new \DateInterval('P10D'));
        $this->type("id=tsk_contract_freeze_startDate", $tenDays->format('Y-m-d'));
        $this->type("id=tsk_contract_freeze_endDate", $this->tomorrow->format('Y-m-d'));
        $this->click("css=input[type=\"submit\"]");
        $this->waitForPageToLoad("30000");
        $this->assertNotContains('Contract freeze saved', $this->getHtmlSource());

        // Check total days < 60
        $sixtyFiveDays = clone $this->tomorrow;
        $sixtyFiveDays->add(new \DateInterval('P65D'));
        $this->type("id=tsk_contract_freeze_startDate", $this->tomorrow->format('Y-m-d'));
        $this->type("id=tsk_contract_freeze_endDate", $sixtyFiveDays->format('Y-m-d'));
        $this->click("css=input[type=\"submit\"]");
        $this->waitForPageToLoad("30000");
        $this->assertNotContains('Contract freeze saved', $this->getHtmlSource());


        // Check overlapping days
        // First make good insert
        $this->type("id=tsk_contract_freeze_startDate", $this->tomorrow->format('Y-m-d'));
        $this->type("id=tsk_contract_freeze_endDate", $tenDays->format('Y-m-d'));
        $this->click("css=input[type=\"submit\"]");
        $this->waitForPageToLoad("30000");
        $this->assertContains('Contract freeze saved', $this->getHtmlSource());


        // Now try overlap
        $this->type("id=tsk_contract_freeze_startDate", $this->tomorrow->format('Y-m-d'));
        $this->type("id=tsk_contract_freeze_endDate", $sixtyFiveDays->format('Y-m-d'));
        $this->click("css=input[type=\"submit\"]");
        $this->waitForPageToLoad("30000");
        $this->assertNotContains('Contract freeze saved', $this->getHtmlSource());
    }


    public function testMyTestCase()
    {
      $this->login();
      $this->open("/app_dev.php/admin/student/register");
      $this->assertContains('Add New Contact', $this->getHtmlSource());
      $this->pause(10000);
      $this->click("link=Add New Contact");
      // $this->waitForPageToLoad("30000");
      $this->type("xpath=//input[contains(@id, 'first')]", "Bobby");
      $this->type("xpath=//input[contains(@id, 'last')]", "Blue Bland");
      $this->type("xpath=//input[contains(@id, 'address1')]", "24 VGC");
      $this->type("xpath=//input[contains(@id, 'city')]", "South Orange");
      $this->select("xpath=//select[contains(@id, 'state')]", "label=NJ");
      $this->type("xpath=//input[contains(@id, 'postalCode')]", "07079");
      $this->type("xpath=//input[contains(@id, 'phone')]", "212-343-2912");
      $this->select("xpath=//select[contains(@id, 'dateOfBirth_year')]", "label=1978");
      $this->select("xpath=//select[contains(@id, 'dateOfBirth_month')]", "label=Apr");
      $this->select("xpath=//select[contains(@id, 'dateOfBirth_day')]", "label=3");
      // $this->pause(2000);
      $this->click("name=btn_create");
      $this->pause(2000);
      $this->click("css=button.craue_formflow_button_last");
      $this->waitForPageToLoad("10000");
      $this->assertContains('Find or Create Emergency Contact', $this->getHtmlSource());

      $this->pause(10000);
      $this->click("link=Add New Contact");
      $this->type("xpath=//input[contains(@id, 'first')]", "Samantha");
      $this->type("xpath=//input[contains(@id, 'last')]", "RayRay");
      $this->type("xpath=//input[contains(@id, 'phone')]", "212-555-1212");
      $this->click("name=btn_create");
      $this->pause(2000);
      $this->click("css=button.craue_formflow_button_last");
      // $this->type("id=*_firstName", "Samantha");
      // $this->type("id=*_lastName", "Ray");
      // $this->type("id=*_phone", "212-555-1212");
    }
    public function login()
    {
        $this->open('/app_dev.php/login');
        $this->type("id=username", "mhill");
        $this->type("id=password", "test");
        $this->click("id=_submit");
        $this->waitForPageToLoad("30000");
        $this->assertContains('Welcome to TSK ERP', $this->getHtmlSource());
    }
}
