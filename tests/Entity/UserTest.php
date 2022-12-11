<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
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

        $this->assertEquals('admin@todoco.fr', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals('admin', $user->getUserIdentifier());
    }

    /* Test getTask() entity method */
    public function checkTasks(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);

        $this->assertEquals(true, !empty($user->getTasks()));
    }

    /* Test addTask() and removeTask() entity method */
    public function testAddRemoveTasks(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $beforeAdding = sizeof($user->getTasks());

        $task = new Task();
        $task->setTitle('Task title');
        $task->setContent('Task content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);

        $user->addTask($task);
        $this->assertEquals($beforeAdding + 1, sizeof($user->getTasks()));

        $user->removeTask($task);
        $this->assertEquals($beforeAdding, sizeof($user->getTasks()));
    }

}