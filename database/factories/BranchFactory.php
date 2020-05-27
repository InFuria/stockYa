<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Branch;
use Faker\Generator as Faker;

$factory->define(Branch::class, function (Faker $faker) {
    return [
        'code' => $faker->postcode,
        'city_id' => 1,
        'company_id' => $faker->numberBetween(1, 50),
        'name' => $faker->name,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
    ];
});
