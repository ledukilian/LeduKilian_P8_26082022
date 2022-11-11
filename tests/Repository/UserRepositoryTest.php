<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    public function testAddEntity(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = new User();
        $user->setEmail('newUser@test.test');
        $user->setPassword('aN3wP4ssw0rd');
        $user->setRoles(['ROLE_USER']);
        $user->setUsername('newUser');

        $userRepository->add($user, true);
        $search = $userRepository->findOneBy(['email' => 'newUser@test.test']);

        $this->assertEquals($user, $search);
    }

    public function testRemoveEntity(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'newUser@test.test']);
        $userRepository->remove($user, true);

        $user = $userRepository->findOneBy(['email' => 'newUser@test.test']);
        $this->assertNull($user);
    }

    public function testUpgradePasswordSuccess(): void
    {
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = $userRepository->findOneBy(['email' => 'admin@todoco.fr']);
        $password = 't$is1sh4sh3dP4ssw0rd';

        $userRepository->upgradePassword($user, $password);

        $this->assertEquals($password, $user->getPassword());
    }


}