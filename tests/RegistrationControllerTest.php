<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationControllerTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    public function testRegistrationWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/register');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRegisterWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/admin/register');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testRegisterWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_admin'];
        $this->login($client, $user);
        $client->request('GET', '/admin/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testRegisterNewUserWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_admin'];
        $this->login($client, $user);
        $crawler = $client->request('GET', '/admin/register');
        $form = $crawler->selectButton('Valider')->form([
            'registration_form[username]' => 'testCreateUser',
            'registration_form[email]' => 'test@createuser.com',
            'registration_form[plainPassword]' => 'testCreateUser',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
