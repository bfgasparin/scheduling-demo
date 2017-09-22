<?php

use Illuminate\Database\Seeder;

class SalonConfigBookingsTableSeeder extends Seeder
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
            factory(\App\Salon\Config\Booking::class)->create([
                'salon_id' => $salon->id,
            ]);
        });
    }
}
