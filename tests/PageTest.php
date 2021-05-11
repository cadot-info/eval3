<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CustomerControllerTest extends WebTestCase
{
    public function testPage()
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(200, $client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Crypto index', $client->getResponse()->getContent());
    }
}
