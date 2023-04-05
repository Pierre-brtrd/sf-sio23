<?php

namespace App\Fixtures\Providers;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HashPasswordProvider
{
    public function __construct(
        private readonly UserPasswordHasherInterface $encoder
    ) {
    }

    public function hashPasswordFixture(string $plainPassword): string
    {
        return $this->encoder->hashPassword(new User, $plainPassword);
    }
}
