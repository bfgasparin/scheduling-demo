<?php

/*
|--------------------------------------------------------------------------
| Salon Factories
|--------------------------------------------------------------------------
|
| Salon factories give a convenient way to create models for testing and
| seeding the database. Here we tell the factory how the salon model
| should look.
|
| @see App\Salon
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon::class, function (Faker\Generator $faker) {
    $categories = config('salon.categories');

    return [
        'description' => $faker->sentence,
        'category' =>  $faker->randomElement($categories),
        'name' => function (array $salon) use ($faker) {
            return "{$faker->name}`s {$salon['category']}"; // like Bob`s SPA
        },
        'email' => $faker->unique()->safeEmail,
    ];
});

