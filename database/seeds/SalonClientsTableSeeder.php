<?php

use Illuminate\Database\Seeder;

class SalonClientsTableSeeder extends Seeder
{
    use InteractsWithFaker, SeededSalons;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = factory(\App\User::class, 20)->states('active')->create();

        $this->salons()->each(function ($salon) use ($users) {
            $this->resetFakerUniqueFlags();
            $users->random(rand(1,20))->each(function ($user) use ($salon) {
                factory(\App\Salon\Client::class)->create([
                    'salon_id' => $salon->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
