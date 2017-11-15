<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 *
 * @package Tests\AppBundle\Controller
 */
class DefaultControllerTest extends WebTestCase
{
    /**
     * Test
     *
     * @return void
     */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        self::assertEquals(200, $client->getResponse()->getStatusCode());
        self::assertContains(
            'Welcome to Symfony',
            $crawler->filter('#container h1')->text()
        );
    }
}
