<?php

/*
|--------------------------------------------------------------------------
| WorkingJorney Absence Factories
|--------------------------------------------------------------------------
|
| WorkingJorney Absence factories give a convenient way to create models
| for testing and seeding the database. Here we tell the factory how
| the WorkingJorney Absence model should look.
|
| @see App\Salon\Professional\WorkingJorney\Absence
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Professional\WorkingJorney\Absence::class, function (Faker\Generator $faker) {
    return [
        'working_jorney_id' => factory(App\Salon\Professional\WorkingJorney::class),
        'date' => function ($absence) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($absence['working_jorney_id'])
                ->professional;

            // insert only valid absence date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterAbsenceOn($date);
            })->dateThisDecade('+1 year');
        },
        'observation' => $faker->sentence,
    ];
})->state(App\Salon\Professional\WorkingJorney\Absence::class, 'past', function ($faker) {
    return [
        'date' => function ($absence) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($absence['working_jorney_id'])
                ->professional;

            // insert only valid absence date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterAbsenceOn($date);
            })->dateBetween('-10 years', 'now');
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Absence::class, 'past_one_year', function ($faker) {
    return [
        'date' => function ($absence) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($absence['working_jorney_id'])
                ->professional;

            // insert only valid absence date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterAbsenceOn($date);
            })->dateBetween('-1 year', 'now');
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Absence::class, 'this_month', function ($faker) {
    return [
        'date' => function ($absence) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($absence['working_jorney_id'])
                ->professional;

            // insert only valid absence date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterAbsenceOn($date);
            })->dateBetween('-1 month', Carbon\Carbon::now()->endOfMonth());
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Absence::class, 'future', function ($faker) {
    return [
        'date' => function ($absence) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($absence['working_jorney_id'])
                ->professional;

            // insert only valid absence date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterAbsenceOn($date);
            })->dateBetween('now', '+1 month');
        },
    ];
});
