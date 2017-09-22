<?php

namespace UseCases\AVEC;

use SalonEmployeesTableSeeder as BaseSeeder;

class SalonEmployeesTableSeeder extends BaseSeeder
{
    use Concerns\AVECSalon;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->salons()->each(function ($salon) {
            factory(\App\Salon\Employee::class)->states('professional', 'not_admin')->create([
                'salon_id' => $salon->id,
                'email' => 'professional@avec.test',
            ]);

            factory(\App\Salon\Employee::class)->states('admin', 'not_professional')->create([
                'salon_id' => $salon->id,
                'email' => 'admin@avec.test',
            ]);

            factory(\App\Salon\Employee::class)->states('admin', 'professional')->create([
                'salon_id' => $salon->id,
                'email' => 'admin_professional@avec.com',
            ]);
        });

        parent::run();
    }
}
