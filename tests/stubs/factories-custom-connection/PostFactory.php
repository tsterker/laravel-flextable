<?php

use Faker\Generator as Faker;
use Tsterker\Tests\Flextable\Stubs\Post as PostWithCustomConnection;

$factory->define(PostWithCustomConnection::class, function (Faker $faker) {
    return [
        'title' => 'CUSTOM TITLE',
    ];
});
