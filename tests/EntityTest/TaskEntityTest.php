<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskEntityTest extends TestCase
{
    private $dateTime;
    private $isDone;
    private $user;

    public function setUp()
    {
        $this->dateTime = new DateTime();
        $this->isDone = false;
        $this->user = new User();
    }

    public function testNewTask()
    {
        $task = new Task();

        $task->setUser($this->user);
        $task->setTitle("title");
        $task->setContent("content");
        $task->setCreatedAt($this->dateTime);

        $this->assertSame(null, $task->getId());
        $this->assertSame($this->user, $task->getUser());
        $this->assertSame("title", $task->getTitle());
        $this->assertSame("content", $task->getContent());
        $this->assertSame($this->dateTime, $task->getCreatedAt());
        $this->assertSame(false, $task->isDone());
    }

    public function testToggle()
    {
        $task = new Task();
        $task->toggle(true);
        $this->assertSame(true, $task->isDone());
    }
}
