<?php

use Illuminate\Database\Seeder;

class SalonsTableSeeder extends Seeder
{
    use SeededSalons;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Salon::class, $this->limit())->create();
    }
}
