<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $roles = ['ROLE_ADMIN', 'ROLE_USER', 'ROLE_EDITOR'];
        $roleAdmin = ['ROLE_ADMIN'];

        // Create admin User
        $user = (new User())
            ->setEmail('pierre@test.com')
            ->setFirstName('Pierre')
            ->setLastName('Bertrand')
            ->setRoles($roleAdmin);

        $pass = $this->hasher->hashPassword($user, 'Test1234');
        $user->setPassword($pass);

        $manager->persist($user);

        // Create 10 random users
        for ($i = 1; $i <= 10; ++$i) {
            $user = (new User())
                ->setEmail("user-$i@test.com")
                ->setFirstName('User')
                ->setLastName($i)
                ->setRoles([$roles[array_rand($roles)]]);

            $pass = $this->hasher->hashPassword($user, "user-$i");
            $user->setPassword($pass);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
