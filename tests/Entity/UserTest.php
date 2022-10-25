<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    public function testValidEntity(): void
    {
        self::bootKernel();

        $user = (new User())
            ->setEmail('admin@todoco.fr')
            ->setPassword('admin')
            ->setRoles(['ROLE_USER'])
            ->setUsername('admin');

        $this->assertEquals('admin@todoco.fr', $user->getEmail());
        $this->assertEquals('admin', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertEquals('admin', $user->getUserIdentifier());
    }

    public function testUserPermissions(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $task = $user->getTasks()->first();

        $this->assertTrue($user->isGranted('edit-task', $task));
        $this->assertTrue($user->isGranted('delete-task', $task));
    }

    public function testTasks(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');
        $task->setCreatedAt(new DateTime());
        $task->setUser($user);
        $task->toggle(false);

        //$this->assertEquals(instanceof Collection, is_array($user->getTasks()));
        //$this->assertEquals($user, $user->addTask($task));
        //$this->assertEquals(true, $user->removeTask($task));
    }

}