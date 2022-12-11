<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    /* Test valid entity with getters and setters */
    public function testValidEntity(): void
    {
        self::bootKernel();

        $user = (new User())
            ->setEmail('admin@todoco.fr')
            ->setPassword('password')
            ->setRoles(['ROLE_USER'])
            ->setUsername('admin');

        $task = new Task();
        $task->setTitle('title');
        $task->setContent('content');
        $task->setCreatedAt(new DateTime());
        $task->setUser($user);
        $task->toggle(false);

        $this->assertEquals('title', $task->getTitle());
        $this->assertEquals('content', $task->getContent());

        $this->assertEquals(true, $task->getUser() instanceof User);
        $this->assertEquals(true, $task->getCreatedAt() instanceof DateTime);

        $this->assertEquals(false, $task->isDone());
    }

}
