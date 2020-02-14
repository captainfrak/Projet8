<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserEntityTest extends TestCase
{
    public function testNewUser()
    {
        $user = new User();
        //set new user
        $user->setEmail("newuser@test.com");
        $user->setUsername("newuser");
        $user->setPassword("password");
        $user->setRoles(['ROLE_USER']);
        $user->addTask(new Task());
        $user->removeTask(new Task());
        //vérif
        $this->assertSame(null, $user->getId());
        $this->assertSame("newuser", $user->getUsername());
        $this->assertSame("password", $user->getPassword());
        $this->assertSame("newuser@test.com", $user->getEmail());
        $this->assertSame(false, $user->isAdmin());
        $this->assertSame(true, $user->isUser());
        $this->assertSame(null, $user->getSalt());
        $this->assertEmpty($user->eraseCredentials());
    }

    public function testUserGetTasks()
    {
        $user = new User();
        $this->assertEmpty($user->getTasks());
    }

    public function testRemoveTask()
    {
        $user = new User();
        $task = new Task();
        $user->addTask($task);
        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());
    }
}
