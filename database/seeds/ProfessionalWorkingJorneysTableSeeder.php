<?php

use Illuminate\Database\Seeder;

class ProfessionalWorkingJorneysTableSeeder extends Seeder
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
            \App\Salon\Employee::professional()->where('salon_id', $salon->id)->get()
                ->each(function ($professional) {
                    factory(\App\Salon\Professional\WorkingJorney::class)->create([
                        'professional_id' => $professional->id,
                    ]);
                });
        });
    }
}
