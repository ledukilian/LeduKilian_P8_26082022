<?php

namespace App\DataFixtures;

use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager): void
    {
        $task = new Task();
        $task->setTitle('Task 1');
        $task->setContent('Task 1 content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Task 2');
        $task->setContent('Task 2 content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setUser($this->getReference(UserFixtures::ADMIN_USER_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Task 3 (Anonyme)');
        $task->setContent('Task 3 content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setUser($this->getReference(UserFixtures::ANONYMOUS_USER_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Task 4');
        $task->setContent('Task 4 content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setUser($this->getReference(UserFixtures::SIMPLE_USER_REFERENCE));
        $manager->persist($task);

        $task = new Task();
        $task->setTitle('Task 5');
        $task->setContent('Task 5 content');
        $task->setCreatedAt(new DateTime());
        $task->toggle(false);
        $task->setUser($this->getReference(UserFixtures::SIMPLE_USER_REFERENCE));
        $manager->persist($task);
        $manager->flush();

    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
