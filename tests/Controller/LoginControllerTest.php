<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginControllerTest extends WebTestCase
{
    public function testLogin(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLogoutWithoutSession(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }


}