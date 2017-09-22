<?php

/*
|--------------------------------------------------------------------------
| Professional WorkingJorney Factories
|--------------------------------------------------------------------------
|
| Professional WorkingJorney factories give a convenient way to create models
| for testing and seeding the database. Here we tell the factory how the
| Professional WorkingJorney model should look.
|
| @see App\Salon\Professional\WorkingJorney
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Salon\Professional\WorkingJorney::class, function (Faker\Generator $faker) {
    // For more readable and real workingJorney data, we creates exit, lunch and entry times as random times
    // multiple of the professional's calendar interval. Final values would be more like 09:15:00 or 10:45:00
    // instead of 09:04:12 or 10:44:00 or 16:28:47 or 18:31:22 or 20:16:00 ...
    return [
        'professional_id' => factory(App\Salon\Employee::class)->states('professional'),
        'calendar_interval' => $faker->randomElement(
            array_values(App\Salon\Config\Booking::CALENDAR_INTERVAL_VALUES)
        ),
        'exit' => function ($workingJorney) use ($faker) {
            return $faker->randomElement(
                time_range('18:00:00', '23:00:00', $workingJorney['calendar_interval'])
            );
        },
        'lunch' => function ($workingJorney) use ($faker) {
            $exit = Carbon\Carbon::parse($workingJorney['exit']);

            // return a lunch value smaller than exit value
            return tap(Carbon\Carbon::today(), function($today) use ($workingJorney, $faker, $exit) {
                $today->addMinutes($faker->randomElement(range(
                    240,
                    $exit->diffInMinutes($today) - 120,
                    $workingJorney['calendar_interval']
                )));
            })->toTimeString();
        },
        'entry' => function ($workingJorney) use ($faker) {
            $lunch = Carbon\Carbon::parse($workingJorney['lunch']);

            // return an entry value smaller than lunch value
            return tap(Carbon\Carbon::today(), function($today) use ($workingJorney, $faker, $lunch) {
                $today->addMinutes($faker->randomElement(range(
                    $workingJorney['calendar_interval'],
                    $lunch->diffInMinutes($today) - 120,
                    $workingJorney['calendar_interval']
                )));
            })->toTimeString();
        },
        'days_of_week' => $faker->randomElements([0,1,2,3,4,5,6], mt_rand(1,6)),
    ];
});
