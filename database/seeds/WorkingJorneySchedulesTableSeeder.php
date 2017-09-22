<?php

use Illuminate\Database\Seeder;
use \App\Salon\Professional\WorkingJorney;

class WorkingJorneySchedulesTableSeeder extends Seeder
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
            WorkingJorney::whereHas('professional', function ($query) use ($salon) {
                $query->where('salon_id', $salon->id);
            })->get()->each(function ($workingJorney) {
                $this->seedSchedules($workingJorney);
            });
        });
    }

    /**
     * Seed random Schedules for the given WorkingJorney
     *
     * @param WorkingJorney $workingJorney
     */
    protected function seedSchedules(WorkingJorney $workingJorney) : void
    {
        repeat(rand(1, 10), function() use ($workingJorney) {
            factory(\App\Salon\Professional\WorkingJorney\Schedule::class)->states('past')->create([
                'working_jorney_id' => $workingJorney->id,
            ]);
        });

        repeat(rand(1, 5), function() use ($workingJorney) {
            factory(\App\Salon\Professional\WorkingJorney\Schedule::class)->states('past_one_year')->create([
                'working_jorney_id' => $workingJorney->id,
            ]);
        });

        factory(\App\Salon\Professional\WorkingJorney\Schedule::class)->states('future')->create([
            'working_jorney_id' => $workingJorney->id,
        ]);
    }
}

