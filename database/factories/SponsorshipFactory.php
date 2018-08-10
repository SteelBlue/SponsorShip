<?php

use Faker\Generator as Faker;

$factory->define(App\Sponsorship::class, function (Faker $faker) {
    return [
        'email' => 'john@example.com',
        'company_name' => 'ExampleSoft, Inc.',
        'amount' => 50000,
    ];
});
