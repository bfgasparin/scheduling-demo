<?php

/*
|--------------------------------------------------------------------------
| WorkingJorney Schedule Factories
|--------------------------------------------------------------------------
|
| WorkingJorney Schedule factories give a convenient way to create models
| for testing and seeding the database. Here we tell the factory how
| the WorkingJorney Schedule model should look.
|
| @see App\Salon\Professional\WorkingJorney\Schedule
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Professional\WorkingJorney\Schedule::class, function (Faker\Generator $faker) {
    // For more readable and real workingJorney data, we creates exit and entry times as random times
    // multiple of the professional's calendar interval. Final values would be more like 09:15:00 or 10:45:00
    // instead of 09:04:12 or 10:44:00 or 16:28:47 or 18:31:22 or 20:16:00 ...
    return [
        'working_jorney_id' => factory(App\Salon\Professional\WorkingJorney::class),
        'date' => function ($schedule) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id'])
                ->professional;

            // insert only valid schedule date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterScheduleOn($date);
            })->dateThisDecade('+1 year');
        },
        'exit' => function ($schedule) use ($faker) {
            $workingJorney = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id']);

            return $faker->randomElement(
                time_range('18:00:00', '23:00:00', $workingJorney['calendar_interval'])
            );
        },
        'entry' => function ($schedule) use ($faker) {
            $exit = Carbon\Carbon::parse($schedule['exit']);

            // return an entry value smaller than exit value
            return tap(Carbon\Carbon::today(), function($today) use ($schedule, $faker, $exit) {
                $workingJorney = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id']);

                $today->addMinutes($faker->randomElement(range(
                    $workingJorney['calendar_interval'],
                    $exit->diffInMinutes($today) - 180,
                    $workingJorney['calendar_interval']
                )));
            })->toTimeString();
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Schedule::class, 'past', function ($faker) {
    return [
        'date' => function ($schedule) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id'])
                ->professional;

            // insert only valid schedule date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterScheduleOn($date);
            })->dateBetween('-10 years', 'now');
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Schedule::class, 'past_one_year', function ($faker) {
    return [
        'date' => function ($schedule) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id'])
                ->professional;

            // insert only valid schedule date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterScheduleOn($date);
            })->dateBetween('-1 year', 'now');
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Schedule::class, 'this_month', function ($faker) {
    return [
        'date' => function ($schedule) use ($faker) {
            $workingJorney = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id'])
                ->professional;

            // insert only valid schedule date for the professional
            return $faker->valid(function($date) use ($workingJorney) {
                return $professional->canRegisterScheduleOn($date);
            })->dateBetween('-1 month', Carbon\Carbon::now()->endOfMonth());
        },
    ];
})->state(App\Salon\Professional\WorkingJorney\Schedule::class, 'future', function ($faker) {
    return [
        'date' => function ($schedule) use ($faker) {
            $professional = App\Salon\Professional\WorkingJorney::find($schedule['working_jorney_id'])
                ->professional;

            // insert only valid schedule date for the professional
            return $faker->valid(function($date) use ($professional) {
                return $professional->canRegisterScheduleOn($date);
            })->dateBetween('now', '+1 month');
        },
    ];
});
