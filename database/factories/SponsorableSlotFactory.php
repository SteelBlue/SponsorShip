<?php

use Faker\Generator as Faker;

$factory->define(App\SponsorableSlot::class, function (Faker $faker) {
    return [
        'publish_date' => now()->addMonths(1),
        'price' => 50000,
    ];
});
