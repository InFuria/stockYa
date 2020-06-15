<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Company;
use Faker\Generator as Faker;

$factory->define(Company::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'slug' => '+'.$faker->company,
        'email' => $faker->companyEmail,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'whatsapp' => $faker->phoneNumber,
        'social' => 'social',
        'city_id' => 1,
        'score' => $faker->randomFloat(2,1, 5),
        'delivery' => $faker->randomFloat(),
        'zone' => 'asdasdadasd',
        'status' => $faker->numberBetween(1,3),
        'attention_hours' => 'asdasdads',
        'category_id' => $faker->numberBetween(1,2)
    ];
});
