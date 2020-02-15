<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    // *** TEST TO GET TO USER LIST *** //

    public function testUsersListWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/users');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersListWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/admin/users');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersListWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_admin'];
        $this->login($client, $user);
        $client->request('GET', '/admin/users');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // *** TEST TO GOT TO THE PAGE TO EDIT A USER *** //

    public function testUsersEditWithoutUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];

        $client->request('GET', '/admin/users/' . $userToEdit->getId() . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersEditWithoutAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/admin/users/' . $userToEdit->getId() . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersEditWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $user = $users['user_admin'];
        $this->login($client, $user);
        $client->request('GET', '/admin/users/' . $userToEdit->getId() . '/edit');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUsersEdition()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $user = $users['user_admin'];
        $this->login($client, $user);
        $crawler = $client->request(
            'GET',
            '/admin/users/' . $userToEdit->getId() . '/edit'
        );
        $form = $crawler->selectButton('Modifier')->form([
            'user[username]' => 'testUserEdit',
            'user[email]' => 'test@test.com',
            'user[password]' => 'testCreateUser1',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testUsersDeleteWithoutUserLoggedIn()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $client->request('GET', '/admin/users/' . $userToEdit->getId() . '/delete' );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersDeleteWithAdminLoggedIn()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_dummy'];
        $user = $users['user_admin'];
        $this->login($client, $user);
        $crawler = $client->request(
            'GET',
            '/admin/users/' . $userToEdit->getId() . '/delete'
        );

        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testUsersChangeRoleWithoutUserLoggedIn()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $client->request('GET', '/admin/changerole/' . $userToEdit->getId() );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testUsersChangeRoleWithAdminLoggedIn()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $userToEdit = $users['user_user'];
        $user = $users['user_admin'];
        $this->login($client, $user);
        $client->request('GET', '/admin/changerole/' . $userToEdit->getId() );

        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');

        $client->request('GET', '/admin/changerole/' . $userToEdit->getId() );

        $this->assertResponseRedirects('/admin/users');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
