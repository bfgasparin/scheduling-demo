<?php

namespace Tests\Feature\API\Concerns;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Contains CRUD tests authenticating with a User.
 *
 * Should be used for CRUDs of resources that belongs to a
 * User.
 *
 * @see App\User
 */
trait UserCRUDTests
{
    /**
     * User authenticated which will test the CRUDs
     *
     * @var App\User
     */
    protected $authUser;

    /** @before */
    public function setUpCRUDAuthUser() : void
    {
        $this->authUser = $this->authUser();
    }

    /**
     * Returns the authenticated user to test the Resource CRUDs
     *
     * @return App\User
     */
    protected function authUser() : User
    {
        return factory(User::class)->states('active')->create();
    }

    /**
     * Setup the application before run the given create CRUD test
     *
     * This method runs after generating CRUD input data, but before call the create resource URI.
     *
     * So you can not use fixtures data created here on for you input data. You can
     * use the before phpunit annotation intead
     * @see self::inputData
     * @see https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.annotations.before
     *
     * @return void
     */
    public function setUpAppBeforeCreateResource() : void
    {
        $this->actingAs($this->authUser, $this->authGuard());
    }

    /**
     * Setup the application before run the given update CRUD test
     *
     * This method runs after generating CRUD input data, but before call the create resource URI.
     *
     * So you can not use fixtures data created here on for you input data. You can
     * use the before phpunit annotation intead
     * @see self::inputData
     * @see https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.annotations.before
     *
     * @return void
     */
    public function setUpAppBeforeUpdateResource() : void
    {
        $this->actingAs($this->authUser, $this->authGuard());
    }

    /**
     * Setup the application before run the given read CRUD test
     *
     * This method runs after generating CRUD input data, but before call the create resource URI.
     *
     * So you can not use fixtures data created here on for you input data. You can
     * use the before phpunit annotation intead
     * @see self::inputData
     * @see https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.annotations.before
     *
     * @return void
     */
    public function setUpAppBeforeReadResource() : void
    {
        $this->actingAs($this->authUser, $this->authGuard());
    }

    /**
     * Setup the application before run the given delete CRUD test
     *
     * This method runs after generating CRUD input data, but before call the create resource URI.
     *
     * So you can not use fixtures data created here on for you input data. You can
     * use the before phpunit annotation intead
     * @see self::inputData
     * @see https://phpunit.de/manual/current/en/phpunit-book.html#appendixes.annotations.before
     *
     * @return void
     */
    public function setUpAppBeforeDeleteResource() : void
    {
        $this->actingAs($this->authUser, $this->authGuard());
    }

    /** @test */
    public function a_user_can_not_delete_the_resource_from_another_user()
    {
        if (!$this->shouldTestOperation('delete')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD delete operations");
        }

        $id = with($model = $this->getExistingResourceFromAnotherUser())->id;

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('DELETE', "/api/{$this->getCRUDResource()}/{$id}")
            ->assertStatus(404);

        $this->assertDatabaseHas($this->getCRUDTable(), $this->getDatabaseDataAfterDeleteFailure($model));
    }

    /** @test */
    public function a_user_can_not_update_the_resource_from_another_user()
    {
        if (!$this->shouldTestOperation('update')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD update operations");
        }

        $id = with($model = $this->getExistingResourceFromAnotherUser())->id;

        $this->actingAs($this->authUser, $this->authGuard())
            ->json( 'PUT', "/api/{$this->getCRUDResource()}/{$id}",
                $this->getInputData())
            ->assertStatus(404);

        $this->assertDatabaseHas($this->getCRUDTable(), $this->getDatabaseDataAfterUpdateFailure($model));
    }

    /** @test */
    public function a_user_can_not_see_the_resource_from_another_user()
    {
        if (!$this->shouldTestOperation('read')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD read operations");
        }

        // fixtures
        $id = $this->getExistingResourceFromAnotherUser()->id;

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('GET', "/api/{$this->getCRUDResource()}/{$id}")
            ->assertStatus(404);
    }

    /** @test */
    public function a_user_can_not_list_resources_from_another_user()
    {
        if (!$this->shouldTestOperation('read')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD read operations");
        }

        // fixtures
        repeat(rand(1,5), function () {
            return $this->getExistingResourceFromAnotherUser();
        });

        $models = Collection::times(20, function () {
            return $this->getExistingResource();
        });

        $this->actingAs($this->authUser, $this->authGuard())
            ->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $models->forPage(1,15)->toArray(),
                20
            );
    }

    /**
     * Returns the existing Resource to change on
     * operation CRUD resource
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    protected function existingResource() : Model
    {
        // return user on the same user as the authenticated user
        return factory($this->model)->create([
            'user_id' => $this->authUser->id,
        ]);
    }

    /**
     * Returns the data to expect to be into database after the insert resource test success
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function databaseData(array $inputData) : array
    {
        // We check here if the user of the resource is the same as the logged user
        $inputData['user_id'] = $this->authUser->id;

        return $inputData;
    }

    /**
     * Returns the data to expect to contains on response after the resource create or update test success
     *
     * @param array $inputData  The request input data
     * @return array
     */
    protected function responseData(array $inputData) : array
    {
        // We check here if the user of the resource is the same as the logged user
        return tap($inputData, function (&$data) {
            $data['user_id'] = $this->authUser->id;
        });
    }

    /**
     * Returns data to expect into database after the update resource test success
     *
     * @param array $inputData  The request input data
     * @param Model $model      The exising model in database
     * @return array
     */
    protected function databaseDataAfterUpdate(array $inputData, Model $model) : array
    {
        // We check here if the user of the model is the same as before
        $inputData['user_id'] = $model->user_id;

        return $inputData;
    }


    /**
     * Returns the Auth Guard to authenticate the user
     *
     * You can declare a $authGuard attribute in your class to customize
     * the authGuard used to authenticated the user on the User CRUD Tests
     *
     *      protected $authGuard = 'my-custom-guard';
     *
     * @return string
     */
    protected function authGuard() : string
    {
        return $this->authGuard ?? 'api-users';
    }

    /**
     * Returns an existing Resource from another user for read, update or delete
     * operations CRUD resources
     *
     * @return Model
     */
    protected function getExistingResourceFromAnotherUser() : Model
    {
        if (method_exists($this, 'existingResourceFromAnotherUser')) {
           return $this->existingResourceFromAnotherUser();
        }

        return factory($this->model)->create();
    }

}
