<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\WebSale;
use Faker\Generator as Faker;

$factory->define(WebSale::class, function (Faker $faker) {
    return [
        'branch_id' => $faker->numberBetween(1,50),
        'client_id' => $faker->numberBetween(1,50),
        'payment_id' => 1,
        'status' => $faker->boolean
    ];
});
