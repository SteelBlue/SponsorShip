<?php

use Faker\Generator as Faker;

$factory->define(App\Sponsorable::class, function (Faker $faker) {
    return [
        'name' => 'Example Podcast',
    ];
});
