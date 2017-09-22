<?php

namespace UseCases\AVEC;

use Illuminate\Database\Seeder;

class SalonsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Salon::class)->create([
            'name' => 'Salão AVEC',
            'email' => 'salaoavev@avec.com',
        ]);
    }
}
