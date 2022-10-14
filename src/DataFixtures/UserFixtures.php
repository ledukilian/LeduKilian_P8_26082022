<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ANONYMOUS_USER_REFERENCE = 'anonymous-user';
    public const ADMIN_USER_REFERENCE = 'admin-user';
    public const SIMPLE_USER_REFERENCE = 'simple-user';
    public const SIMPLE_USER2_REFERENCE = 'simple-user2';
    /* Demo/test password defined "test" */
    public const DEMO_PASSWORD = '$2y$13$yvfVbjRRV3B5XDQK6vTZBedJd6AZNe0mNkwA.3PMpo.mKQOujGvy6';

    public function load(ObjectManager $manager): void
    {
        /*  Creating annonymous user */
        $userAnon = new User();
        $userAnon->setUsername('Anonyme');
        $userAnon->setEmail('anonyme@todoco.fr');
        $userAnon->setRoles(["ROLE_ANONYMOUS"]);
        $userAnon->setPassword($this::DEMO_PASSWORD);

        /*  Creating admin user */
        $userAdmin = new User();
        $userAdmin->setUsername('Administrateur');
        $userAdmin->setEmail('admin@todoco.fr');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this::DEMO_PASSWORD);

        /*  Creating simple user */
        $simpleUser = new User();
        $simpleUser->setUsername('JudasBricot');
        $simpleUser->setEmail('judas.bricot@todoco.fr');
        $simpleUser->setRoles(["ROLE_USER"]);
        $simpleUser->setPassword($this::DEMO_PASSWORD);

        /*  Creating simple user */
        $simpleUser2 = new User();
        $simpleUser2->setUsername('AlonzoSki');
        $simpleUser2->setEmail('alonzo.ski@todoco.fr');
        $simpleUser2->setRoles(["ROLE_USER"]);
        $simpleUser2->setPassword($this::DEMO_PASSWORD);

        $manager->persist($userAnon);
        $manager->persist($userAdmin);
        $manager->persist($simpleUser);
        $manager->persist($simpleUser2);
        $manager->flush();

        $this->addReference(self::ANONYMOUS_USER_REFERENCE, $userAnon);
        $this->addReference(self::ADMIN_USER_REFERENCE, $userAdmin);
        $this->addReference(self::SIMPLE_USER_REFERENCE, $simpleUser);
        $this->addReference(self::SIMPLE_USER2_REFERENCE, $simpleUser2);
    }
}
