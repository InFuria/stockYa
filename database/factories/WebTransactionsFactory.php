<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\WebTransactions;
use Faker\Generator as Faker;

$factory->define(WebTransactions::class, function (Faker $faker) {
    return [
        'transaction_id' => $faker->numberBetween(1,20),
        'user_id' => $faker->numberBetween(1, 50),
        'status' => $faker->boolean
    ];
});
