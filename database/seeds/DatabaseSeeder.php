<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
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
}
