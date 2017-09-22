<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\User::class, 5)->states('inactive')->create();
        factory(\App\User::class, 10)->states('active')->create();

        factory(\App\User::class)->states('active')->create([
            'name' => 'User',
            'email' => 'user@email.com',
            'password' => 'Secret123',
        ]);
    }
}
