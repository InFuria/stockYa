<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\WebSaleRecord;
use Faker\Generator as Faker;

$factory->define(WebSaleRecord::class, function (Faker $faker) {
    return [
        'transaction_id' => $faker->numberBetween(1,20),
        'user_id' => $faker->numberBetween(1, 50),
        'status' => $faker->boolean
    ];
});
