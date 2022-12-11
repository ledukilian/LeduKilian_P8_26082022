<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{

    /* Test if users list is working as Admin */
    public function testUserList(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $client->request('GET', '/users');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des utilisateurs');

    }

    /* Test if users list is not working as a user (permission denied) */
    public function testUserListWithoutSession(): void {
        $client = static::createClient();
        $crawler = $client->request('GET', '/users');

        $this->assertResponseRedirects("http://localhost/login");
    }

    /* Test if user editing is working (permission allowed) */
    public function testUserEditAllowed(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $client->request('GET', '/users/'.$user->getId().'/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier');
    }

    /* Test if user adding is working (permission allowed) */
    public function testUserAdd(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $client->request('GET', '/users/create');

        $crawler = $client->submitForm('Ajouter', [
            'user[username]' => 'TestUsername',
            'user[password][first]' => 'pa55w0rd',
            'user[password][second]' => 'pa55w0rd',
            'user[email]' => 'test@test.test',
            'user[roles]' => 'ROLE_USER',
        ]);

        //$this->assertResponseIsSuccessful();

        $testUser = $userRepository->findBy(['username' => "TestUsername"])[0];
        $this->assertEquals("TestUsername", $testUser->getUsername());

    }

    /* Test user adding with error */
    public function testUserAddWithDuplicatedUsername(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $client->request('GET', '/users/create');

        $crawler = $client->submitForm('Ajouter', [
            'user[username]' => 'TestUsername',
            'user[password][first]' => 'pa55w0rd',
            'user[password][second]' => 'pa55w0rd',
            'user[email]' => 'test@test',
            'user[roles]' => 'ROLE_USER',
        ]);

        //$this->assertResponseIsSuccessful();
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    /* Test user editing with error */
    public function testEditUserWithInvalidData(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);

        $client->followRedirects();

        $crawler = $client->request('GET', '/users/'.$admin->getId().'/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Submit the form with invalid data (existing username)
        $crawler = $client->submitForm('Modifier', [
            'user[username]' => 'AlonzoSki',
        ]);

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    /* Test user editing on forbidden user (permission denied) */
    public function testEditForbiddenUser(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findBy(['email' => "alonzo.ski@todoco.fr"])[0];
        $client->loginUser($user);

        $client->followRedirects();

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    /* Test user editing with valid data and permission */
    public function testEditUserWithValidData(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);

        $admin = $userRepository->findBy(['email' => "admin@todoco.fr"])[0];
        $client->loginUser($admin);
        $client->followRedirects();


        $userTest = $userRepository->findBy(['username' => "JudasBricot"])[0];

        $crawler = $client->request('GET', '/users/'.$userTest->getId().'/edit');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->selectButton("Modifier")->form();

        $this->assertEquals("JudasBricot", $form["user[username]"]->getValue());
        $this->assertEquals("judas.bricot@todoco.fr", $form["user[email]"]->getValue());

        $form["user[email]"]->setValue("judas.nanas@todoco.fr");
        $form["user[password][first]"]->setValue("pa55w0rd");
        $form["user[password][second]"]->setValue("pa55w0rd");
        $client->submit($form);

        $modifiedUser = $userRepository->findOneBy(['email' => "judas.nanas@todoco.fr"]);

        $this->assertEquals(true, !empty($modifiedUser));
        $this->assertSelectorExists('div', "L'utilisateur a bien été modifié");
    }


}
