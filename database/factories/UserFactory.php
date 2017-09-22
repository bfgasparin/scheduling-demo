<?php

/*
|--------------------------------------------------------------------------
| User Factories
|--------------------------------------------------------------------------
|
| User factories give a convenient way to create models for testing and
| seeding the database. Here we tell the factory how the user model
| should look.
|
| @see App\User
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) { return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'cellphone' => $faker->cellphoneNumber(false),
        'password' => 'Secret123',
    ];
})->state(App\User::class, 'active', function ($faker) {
    return [
        'active' => true,
    ];
})->state(App\User::class, 'inactive', function ($faker) {
    return [
        'active' => false,
    ];
});
