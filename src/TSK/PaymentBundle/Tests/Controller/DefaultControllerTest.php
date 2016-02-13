<?php

namespace TSK\PaymentBundle\Tests\Controller;

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
        $crawler = $this->client->request('GET', '/admin/student/register');
        $this->assertRegExp('/Collect Student Info/', $this->client->getResponse()->getContent());
        // $link = $crawler->filter('a:contains("List")')->eq(0)->link();
        // $crawler = $this->client->click($link);

        // $link = $crawler->filter('a:contains("Tiny")')->eq(0)->link();
        // $crawler = $this->client->click($link);
        // ld($this->client->getResponse()->getContent());
        // $this->assertRegExp('/Collect Student Info/', $this->client->getResponse()->getContent());
    }
}
