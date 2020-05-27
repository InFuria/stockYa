<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'email' => $faker->companyEmail,
        'score' => $faker->randomFloat(2,1, 5),
        'delivery' => $faker->boolean,
        'status' => $faker->numberBetween(1,3),
        'category_id' => $faker->numberBetween(1,5),
    ];
});
