<?php

/*
|--------------------------------------------------------------------------
| Salon Service Factories
|--------------------------------------------------------------------------
|
| Salon Service factories give a convenient way to create models for testing
| and seeding the database. Here we tell the factory how the Salon
| Service model should look.
|
| @see App\Salon\Service
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Service::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->unique()->salonServiceName,
        'description' => $faker->sentence,
        'price' => $faker->randomFloat(2, 1, 999),
        'duration' => $faker->numberBetween(5, 999),
        'cost' => $faker->randomFloat(2, 0, 999),
        'client_visibility' => $faker->randomElement(App\Salon\Service::CLIENT_VISIBILITIES),
        'salon_id' => factory(App\Salon::class),
    ];
})->state(App\Salon\Service::class, 'always_visible_for_clients', function ($faker) {
    return [
        'client_visibility' => 'always',
    ];
})->state(App\Salon\Service::class, 'never_visible_for_clients', function ($faker) {
    return [
        'client_visibility' => 'never',
    ];
})->state(App\Salon\Service::class, 'always_visible_for_guests', function ($faker) {
    return [
        'client_visibility' => 'always',
    ];
})->state(App\Salon\Service::class, 'never_visible_for_guests', function ($faker) {
    return [
        'client_visibility' => 'never',
    ];
});
