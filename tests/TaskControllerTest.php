<?php

namespace App\Tests;

use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
{
    use FixturesTrait;
    use LoginTrait;

    public function testTasksListWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    public function testTasksListWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/tasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTaskDoneWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/tasksdone');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    public function testTaskDoneWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/tasksdone');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAnonymousTaskListWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/anotasks');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testAnonymousTaskListWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/admin/anotasks');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testAnonymousTaskListWithAdmin()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_admin'];
        $this->login($client, $user);
        $client->request('GET', '/admin/anotasks');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTaskCreateWithoutUser()
    {
        $client = static::createClient();
        $client->request('GET', '/tasks/create');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testTasksCreateWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $client->request('GET', '/tasks/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTasksCreationWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $crawler = $client->request('GET', '/tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'test-create',
            'task[content]' => 'test-create',
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/tasks');
        $client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testTaskEditWithoutUser()
    {
        $client = static::createClient();
        $tasks = $this->loadFixtureFiles([__DIR__ . '/testsFixture/task.yaml']);
        $task = $tasks['task_1'];
        $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    // TODO refacto this shit
    /*public function testTaskEditWithUser()
    {
        $client = static::createClient();
        $users = $this->loadFixtureFiles([__DIR__ . '/testsFixture/user.yaml']);
        $user = $users['user_user'];
        $this->login($client, $user);
        $tasks = $this->loadFixtureFiles([__DIR__ . '/testsFixture/task.yaml']);
        $task = $tasks['task_1'];
        $client->request('GET', '/tasks/' . $task->getId() . '/edit');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }*/

    public function testTaskDeleteWithoutUser()
    {
        $client = static::createClient();
        $tasks = $this->loadFixtureFiles([__DIR__ . '/testsFixture/task.yaml']);
        $task = $tasks['task_1'];
        $client->request('GET', '/tasks/' . $task->getId() . '/delete');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function taskChangeWithoutUser()
    {
        $client = static::createClient();
        $tasks = $this->loadFixtureFiles([__DIR__ . '/testsFixture/task.yaml']);
        $task = $tasks['task_1'];
        $client->request('GET', '/admin/tasks/' . $task->getId());

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
