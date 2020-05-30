<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'slug' => $faker->slug,
        'name' => $faker->name,
        'description' => $faker->text,
        'type' => 'promo',
        'image' => $faker->text,
        'price' => $faker->randomNumber(),
        'category_id' => $faker->numberBetween(1, 50),
        'company_id' => $faker->numberBetween(1, 25),
        'score' => $faker->randomFloat(2,0,5),
        'score_count' => $faker->numberBetween(1,15),
        'status' => $faker->boolean
    ];
});
