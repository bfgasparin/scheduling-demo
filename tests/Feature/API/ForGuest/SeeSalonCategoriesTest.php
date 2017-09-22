<?php

namespace Tests\Feature\API\ForGuest;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Testing a non authenticated user seeing salons category
 */
class SeeSalonCategoriesTest extends TestCase
{
    /** @test */
    public function a_guest_can_see_the_salon_categories_list()
    {
        $this->get('api/salonCategories')
            ->assertSuccessful()
            ->assertExactJson([
                'Hair',
                'Depilation',
                'Manicure and Pedicure',
                'Make Up',
                'SPA',
                'Barber Shop',
                'Others',
        ]);
    }
}
