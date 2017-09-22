<?php

use Illuminate\Database\Seeder;
use App\Salon\{Client, Employee};

class ClientBookingsTableSeeder extends Seeder
{
    use SeededSalons;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->salons()->each(function ($salon) {
            $salon->clients()->each(function ($client) use ($salon) {
                $professionals = \App\Salon\Employee::professional()->where('salon_id', $salon->id)->get();

                repeat(rand(4, 10), function () use ($client, $professionals) {
                    $this->seedBooking($client, $professionals->random(), 'canceled');
                });

                repeat(rand(8, 16), function () use ($client, $professionals) {
                    $this->seedBooking($client, $professionals->random(), 'past');
                });

                repeat(rand(10, 20), function () use ($client, $professionals) {
                    $this->seedBooking($client, $professionals->random(), 'past_one_year');
                });

                repeat(rand(4, 10), function () use ($client, $professionals) {
                    $this->seedBooking($client, $professionals->random(), 'this_month');
                });

                $this->seedBooking($client, $professionals->random(), 'future');
            });
        });
    }

    /**
     * Seed a random Booking for the given client and the given professional
     *
     * @param Client $client
     * @param Professional $professional
     * @param ...$states The model factory states to be applied when creating the booking
     */
    protected function seedBooking(Client $client, Employee $professional, ...$states) : void
    {
        retry(1000, function () use ($client, $professional, $states) {
            $booking = factory(\App\Salon\Client\Booking::class)->states(...$states)->make([
                'professional_id' => $professional->id,
                'service_id' => $professional->services->random()->id,
                'client_id' => $client->id,
            ]);

            $professional->validateNewBookingWith(
                $booking->date,
                $booking->start,
                $booking->service
            );

            $booking->save();
        });
    }
}
