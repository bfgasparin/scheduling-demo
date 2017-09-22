<?php

/*
|--------------------------------------------------------------------------
| Salon Client Factories
|--------------------------------------------------------------------------
|
| Salon Client factories give a convenient way to create models for testing and
| seeding the database. Here we tell the factory how the client model
| should look.
|
| @see App\Salon\Client
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Client::class, function (Faker\Generator $faker) {
    return [
        'salon_id' => factory(App\Salon::class),
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'cellphone' => $faker->cellphoneNumber(false),
        'user_id' => factory(App\User::class)->states('active'),
    ];
});
