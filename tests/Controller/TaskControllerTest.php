<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $crawler = $client->request('GET', '/tasks/create');


        $crawler = $client->submitForm('Ajouter', [
            'task[title]' => 'Test task',
            'task[content]' => 'This is a test content',
        ]);

        //$this->assertResponseIsSuccessful();
        //$this->assertSelectorExists('div', "Superbe ! La tâche a bien été ajoutée.");
        //$this->assertSelectorTextContains('div', "Superbe ! La tâche a bien été ajoutée.");
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function testTaskAddAnonymous(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/tasks/create');

        $crawler = $client->submitForm('Ajouter', [
            'task[title]' => 'Test task',
            'task[content]' => 'This is a test content',
        ]);

        //$this->assertResponseIsSuccessful();
        //$this->assertSelectorExists('div', "Superbe ! La tâche a bien été ajoutée.");
        //$this->assertSelectorTextContains('div', "Superbe ! La tâche a bien été ajoutée.");
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
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

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);

        $taskTest = $admin->getTasks()[0];

        $crawler = $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

    }

    public function testDeleteWithoutPermission(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];

        $taskTest = $admin->getTasks()[0];

        $crawler = $client->request('GET', '/tasks/'.$taskTest->getId().'/delete');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

    }

    public function testTaskEditWithoutPermission(): void
    {

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

}