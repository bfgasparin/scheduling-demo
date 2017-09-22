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
$factory->define(App\Salon\Client\Booking::class, function (Faker\Generator $faker) {
    return [
        'client_id' => factory(App\Salon\Client::class),
        'professional_id' => function ($booking) {
            $client = App\Salon\Client::find($booking['client_id']);

            // create a professional on the same salon as the client
            return factory(App\Salon\Employee::class)->states('professional')->create([
                'salon_id' => $client->salon_id
            ])->id;
        },
        'service_id' => function ($booking) {
            $professional = App\Salon\Employee::professional()->find($booking['professional_id']);
            $service = factory(App\Salon\Service::class)->states('always_visible_for_guests')->create([
                'salon_id' => $professional->salon_id
            ]);

            // create a service that is offered by the professional in the booking
            return tap($service->id, function ($id) use ($professional) {
                $professional->attachService($id);
            });
        },
        'date' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            return $faker->valid(function($date) use ($workingJorney) {
                return $workingJorney->representsDate($date);
            })->dateBetween('today', '+1 month');
        },
        'start' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            // than return a time that is contained in the professinal WorkingJorney
            return $faker->randomElement(
                $workingJorney->getCalendarIntervalRangeOn($booking['date'])->toArray()
            );
        },
    ];
})->state(App\Salon\Client\Booking::class, 'canceled', function ($faker) {
    return [
        'canceled_at' => function ($booking) use ($faker) {
            return $faker->dateTimeThisDecade($booking['date']);
        },
    ];
})->state(App\Salon\Client\Booking::class, 'past', function ($faker) {
    return [
        'date' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            return $faker->valid(function($date) use ($workingJorney) {
                return $workingJorney->representsDate($date);
            })->dateThisCentury;
        },
    ];
})->state(App\Salon\Client\Booking::class, 'past_one_year', function ($faker) {
    return [
        'date' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            return $faker->valid(function($date) use ($workingJorney) {
                return $workingJorney->representsDate($date);
            })->dateThisYear;
        },
    ];
})->state(App\Salon\Client\Booking::class, 'this_month', function ($faker) {
    return [
        'date' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            return $faker->valid(function($date) use ($workingJorney) {
                return $workingJorney->representsDate($date);
            })->dateThisMonth;
        },
    ];
})->state(App\Salon\Client\Booking::class, 'future', function ($faker) {
    return [
        'date' => function ($booking) use ($faker) {
            // search for the professional WorkingJorney. If not foound, create a new one and use it
            $workingJorney = factory(App\Salon\Professional\WorkingJorney::class)
                ->firstOrCreate(['professional_id' => $booking['professional_id']]);

            return $faker->valid(function($date) use ($workingJorney) {
                return $workingJorney->representsDate($date);
            })->dateBetween('now', '+1 month');
        },
    ];
});
