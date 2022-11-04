<?php

namespace App\Tests\Controller;

use App\Kernel;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Bienvenue');
        //$this->assertSelectorTextContains('a', 'Se connecter');
    }

    public function testHomePageLoggedIn(): void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $client->loginUser($user);
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Bienvenue');
        $this->assertSelectorExists('a', 'Se déconnecter');
    }

    /* Test if the page contains a button "Se connecter" */
    public function testPageContainsLoginButton(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertSelectorExists('a', 'Se connecter');
    }


}
