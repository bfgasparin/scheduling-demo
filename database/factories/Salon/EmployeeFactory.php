<?php

/*
|--------------------------------------------------------------------------
| Salon Employee Factories
|--------------------------------------------------------------------------
|
| Salon Employee factories give a convenient way to create models for testing
| and seeding the database. Here we tell the factory how the Salon
| Employee model should look.
|
| @see App\Salon\Employee
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Employee::class, function (Faker\Generator $faker) {

    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => 'secret',
        'is_admin' => $faker->boolean,
        'is_professional' => $faker->boolean,
        'salon_id' => factory(App\Salon::class),
    ];
})->state(App\Salon\Employee::class, 'professional', function ($faker) {
    return [
        'is_professional' => true,
    ];
})->state(App\Salon\Employee::class, 'not_professional', function ($faker) {
    return [
        'is_professional' => false,
    ];
})->state(App\Salon\Employee::class, 'admin', function ($faker) {
    return [
        'is_admin' => true,
    ];
})->state(App\Salon\Employee::class, 'not_admin', function ($faker) {
    return [
        'is_admin' => false,
    ];
});
