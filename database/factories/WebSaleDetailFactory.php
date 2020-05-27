<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\WebSaleDetail;
use Faker\Generator as Faker;

$factory->define(WebSaleDetail::class, function (Faker $faker) {
    return [
        'web_sale_id' => $faker->numberBetween(1,20),
        'product_id' => $faker->numberBetween(1,20),
        'quantity' => $faker->numberBetween(1, 50),
        'total' => 0,
    ];
});
