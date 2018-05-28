<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

// GENERATE RANDOM DATA FOR TABLE: users
$factory->define(App\User::class, function(Faker\Generator $faker) {
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'email' => $faker->email,
        'password' => '22101982',
        'verified' => true,
        'user_level' => 1,
        'remember_token' => str_random(10),
        'verification_token' => null,
        'wrote_review' => false,
        'address' => $faker->streetAddress,
        'city' => $faker->city,
        'state' => $faker->state,
        'postcode' => $faker->postcode,
        'country' => $faker->country,
        'phone' => $faker->phoneNumber,
        'cellphone' => $faker->phoneNumber
    ];
});