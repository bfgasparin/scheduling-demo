<?php

use Illuminate\Database\Seeder;

class SalonEmployeesTableSeeder extends Seeder
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

            factory(\App\Salon\Employee::class, 10)->create([
                'salon_id' => $salon->id,
            ]);

            factory(\App\Salon\Employee::class)->states('professional', 'not_admin')->create([
                'salon_id' => $salon->id,
            ]);

            factory(\App\Salon\Employee::class)->states('admin', 'not_professional')->create([
                'salon_id' => $salon->id,
            ]);

            factory(\App\Salon\Employee::class)->states('admin', 'professional')->create([
                'salon_id' => $salon->id,
            ]);
        });
    }
}
