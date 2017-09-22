<?php

/*
|--------------------------------------------------------------------------
| Salon Booking Factories
|--------------------------------------------------------------------------
|
| Salon Booking factories give a convenient way to create models
| for testing and seeding the database. Here we tell the factory how the
| Salon Booking model should look.
|
| @see App\Salon\Client\Booking
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Config\Booking::class, function (Faker\Generator $faker) {
    return [
        'salon_id' => factory(App\Salon::class),
        'cancel_tolerance_for_client_user' => $faker->numberBetween(1, 480),
        'create_tolerance_for_client_user' => $faker->numberBetween(1, 480),
        'calendar_interval' => $faker->randomElement(
            array_values(App\Salon\Config\Booking::CALENDAR_INTERVAL_VALUES)
        ),
    ];
});
