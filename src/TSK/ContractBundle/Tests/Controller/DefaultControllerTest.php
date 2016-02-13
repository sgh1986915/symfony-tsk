<?php

namespace TSK\ContractBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    private $client;

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('_submit')->form();
        
        $form['_username'] = 'mhill';
        $form['_password'] = 'test';
        $crawler = $this->client->submit($form);
    }

    public function testIndex()
    {
        // $client = static::createClient();
        $crawler = $this->client->request('GET', 'admin/tsk/student/student/list');
        $this->assertTrue($crawler->filter('html:contains("Contract")')->count() > 0);


    }
}
