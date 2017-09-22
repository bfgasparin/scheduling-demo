<?php

namespace UseCases\AVEC;

use UsersTableSeeder;
use InteractsWithFaker;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use InteractsWithFaker;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SalonsTableSeeder::class);
        $this->call(SalonConfigBookingsTableSeeder::class);
        $this->call(SalonServicesTableSeeder::class);
        $this->call(SalonEmployeesTableSeeder::class);
        $this->call(ProfessionalWorkingJorneysTableSeeder::class);
        $this->call(WorkingJorneySchedulesTableSeeder::class);
        $this->call(WorkingJorneyAbsencesTableSeeder::class);
        $this->call(ProfessionalServiceTableSeeder::class);
        $this->call(SalonClientsTableSeeder::class);
        $this->call(ClientBookingsTableSeeder::class);
    }

    /**
     * Seed the given connection from the given path.
     * and reseet the faker's unique flags
     *
     * @param  string  $class
     * @return void
     */
    public function call($class)
    {
        parent::call($class);

        $this->resetFakerUniqueFlags();
    }

}
