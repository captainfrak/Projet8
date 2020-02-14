<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ControllerTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    public function testIndexWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testIndexWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);

        $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}
