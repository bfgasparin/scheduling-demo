<?php

use Illuminate\Database\Seeder;

class SalonServicesTableSeeder extends Seeder
{
    use InteractsWithFaker, SeededSalons;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->salons()->each(function ($salon) {
            $this->resetFakerUniqueFlags();

            factory(\App\Salon\Service::class, rand(2,4))->states('never_visible_for_clients')->create([
                'salon_id' => $salon->id,
            ]);

            factory(\App\Salon\Service::class, rand(10,15))->states('always_visible_for_clients')->create([
                'salon_id' => $salon->id,
            ]);
        });
    }
}
