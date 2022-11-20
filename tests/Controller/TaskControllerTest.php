<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Security;

class TaskControllerTest extends WebTestCase
{
    public function testTaskList(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/tasks');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/tasks/done');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testTaskAdd(): void
    {
        $client = static::createClient();
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/tasks/create');


        $crawler = $client->submitForm('Ajouter', [
            'task[title]' => 'Test task',
            'task[content]' => 'This is a test content',
        ]);

        $searchTask = $taskRepository->findOneBy(['title' => 'Test task']);
        $this->assertEquals(true, !empty($searchTask));
    }

    public function testTaskAddAnonymous(): void
    {
        $client = static::createClient();
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $crawler = $client->request('GET', '/tasks/create');

        $crawler = $client->submitForm('Ajouter', [
            'task[title]' => 'Another test task',
            'task[content]' => 'This is a test content',
        ]);

        $searchTask = $taskRepository->findOneBy(['title' => 'Another test task']);
        $this->assertEquals(true, !empty($searchTask));
    }

    public function testToggleTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);

        $taskTest = $admin->getTasks()[0];
        $this->assertEquals(false, $taskTest->isDone());

        $crawler = $client->request('GET', '/tasks/'.$taskTest->getId().'/toggle');

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $taskTest = $taskRepository->findBy(['id' => $taskTest->getId()])[0];

        $this->assertEquals(true, $taskTest->isDone());
    }

    public function testDeleteTask(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $admin = $userRepository->findOneBy(['email' => "admin@todoco.fr"]);
        $annon = $userRepository->findOneBy(['email' => "anonyme@todoco.fr"]);
        $client->loginUser($admin);

        $taskTest = $admin->getTasks()[0]->getId();
        $crawler = $client->request('GET', '/tasks/'.$taskTest.'/delete');

        $annonTask = $annon->getTasks()[0]->getId();
        $crawler = $client->request('GET', '/tasks/'.$annonTask.'/delete');

        $searchTask = $taskRepository->findOneBy(['id' => $taskTest]);
        $this->assertEquals(true, empty($searchTask));

        $searchTask = $taskRepository->findOneBy(['id' => $annonTask]);
        $this->assertEquals(false, empty($searchTask));
    }

    public function testDeleteWithoutPermission(): void
    {
       $client = static::createClient();
       $userRepository = static::getContainer()->get(UserRepository::class);

       $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];

       $taskTest = $admin->getTasks()[0];

       $crawler = $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');

       $this->assertEquals($taskTest->getUser(), $admin);
    }

    public function testTaskEditWithoutPermission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $user = $userRepository->findOneBy(['email' => "judas.bricot@todoco.fr"]);
        $client->loginUser($user);
        $client->followRedirects();

        $taskTest = $taskRepository->findOneBy(['title' => "Test task"]);
        $crawler = $client->request('GET', 'tasks/'.$taskTest->getId().'/edit');

        $this->assertNotEquals($user->getId(), $taskTest->getUser()->getId());

        $this->assertSelectorExists('div', "Oops ! Vous n'avez pas les droits pour modifier cette tâche.");

    }

    public function testTaskEditWithPermission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);
        $client->followRedirects();

        $taskTest = $admin->getTasks()[0];

        $crawler = $client->request('GET', 'tasks/'.$taskTest->getId().'/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals($taskTest->getTitle(), $form["task[title]"]->getValue());
        $this->assertEquals($taskTest->getContent(), $form["task[content]"]->getValue());

        $form["task[title]"]->setValue("This is the new title");
        $client->submit($form);

        $modifiedTask = $taskRepository->findOneBy(['title' => "This is the new title"]);

        $this->assertEquals(true, !empty($modifiedTask));
        $this->assertSelectorExists('div', "Superbe ! La tâche a bien été modifiée.");
    }

    public function testEditForeignTaskAsAdmin(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $taskRepository = static::getContainer()->get(TaskRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);
        $client->followRedirects();

        $taskTest = $taskRepository->findBy(['title' => "Task 5"])[0];

        $crawler = $client->request('GET', 'tasks/'.$taskTest->getId().'/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals($taskTest->getTitle(), $form["task[title]"]->getValue());
        $this->assertEquals($taskTest->getContent(), $form["task[content]"]->getValue());

        $form["task[title]"]->setValue("Admin edit test");
        $client->submit($form);

        $modifiedTask = $taskRepository->findOneBy(['title' => "Admin edit test"]);

        $this->assertEquals(true, !empty($modifiedTask));
        $this->assertSelectorExists('div', "Superbe ! La tâche a bien été modifiée.");
    }

    public function testVoter(): void
    {
        $task = null;
        $security = static::getContainer()->get(Security::class);
        $result = $security->isGranted('edit-task', $task);

        $this->assertNotEquals(true, $result);
    }
}