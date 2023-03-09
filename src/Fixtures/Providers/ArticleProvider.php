<?php

namespace App\Fixtures\Providers;

use Faker\Factory;
use Faker\Generator;

class ArticleProvider
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function generateDate(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear());
    }
}
