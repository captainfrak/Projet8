<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityControllerTest extends WebTestCase
{
    use FixturesTrait;

    public function testLoginPage()
    {
        $client = static::createClient();
        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLoginPageWrongCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se Connecter')->form([
            'username' => 'testUser1',
            'password' => 'testUser1'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginPageGoodCredentials()
    {
        $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $client = static::createClient();

        //$crawler = $client->request('GET', '/login');
        //$form = $crawler->selectButton('Se Connecter')->form([
        //    'username' => 'testUser',
        //    'password' => 'testUser'
        //]);
        //$client->submit($form);

        $csrfToken = $client
            ->getContainer()
            ->get('security.csrf.token_manager')
            ->getToken('authenticate');
        $client->request('POST', '/login', [
            '_csrf_token' => $csrfToken,
            'username' => 'testUser',
            'password' => 'testUser'
        ]);
        $this->assertResponseRedirects('/');
        $client->followRedirect();
        $this->assertSelectorNotExists('.alert.alert-danger');
    }
}
