<?php

use Faker\Generator as Faker;
use Tsterker\Tests\Flextable\Stubs\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
    ];
});
