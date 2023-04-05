<?php

namespace App\Tests\Traits;

trait AssertTestTrait
{
    public function assertHasErrors(mixed $entity, int $number = 0): void
    {
        /* On initialiser le Kernel Symfony */
        self::bootKernel();

        $errors = self::getContainer()->get('validator')->validate($entity);

        /* On initialise une liste de messages d'erreur, pour le moment */
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() .  ' -> '  . $error->getMessage();
        }

        /* On test le nombre d'erreur */
        $this->assertCount($number, $errors, implode(', ', $messages));
    }
}
