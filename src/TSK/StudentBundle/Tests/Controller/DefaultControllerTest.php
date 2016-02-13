<?php

namespace TSK\StudentBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/admin/student/register');

        $this->assertTrue($crawler->filter('html:contains("login")')->count() > 0);
    }
}
