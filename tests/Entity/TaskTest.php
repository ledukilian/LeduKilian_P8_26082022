<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
    public function testValidEntity(): void
    {
        self::bootKernel();

        $user = (new User())
            ->setEmail('admin@todoco.fr')
            ->setPassword('admin')
            ->setRoles(['ROLE_USER'])
            ->setUsername('admin');

        $task = new Task();
        $task->setTitle('test');
        $task->setContent('test');
        $task->setCreatedAt(new \DateTime());
        $task->setUser($user);
        $task->toggle(false);

        $this->assertEquals('test', $task->getTitle());
        $this->assertEquals('test', $task->getContent());
        $this->assertEquals(true, $task->getUser() instanceof User);
        $this->assertEquals(true, $task->getCreatedAt() instanceof DateTime);
        $this->assertEquals('admin', $user->getUserIdentifier());
        $this->assertEquals(false, $task->isDone());
    }

    public function checkUserTaskRelation(): void
    {
        self::bootKernel();

        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);

        $this->assertEquals(false, empty($user->getTasks()));

    }

}
