<?php

use Illuminate\Database\Seeder;

class ProfessionalServiceTableSeeder extends Seeder
{
    use CustomCollections, SeededSalons;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed Relation between Services and Professionals
        $this->salons()->each(function ($salon) {
            $services = \App\Salon\Service::where('salon_id', $salon->id)->get();

            \App\Salon\Employee::professional()->where('salon_id', $salon->id)->get()
                ->each(function ($professional) use ($services) {
                    $professional->attachService($services->random(rand(5,10))->toProfessionalServiceAttributes());
                });
        });
    }

    /**
     * Run the database seeds.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function __invoke()
    {
        $this->decorateEloquentCollection();

        return parent::__invoke();
    }

}
