<?php

namespace Tests\Feature\API\Concerns;

use Illuminate\Support\Collection;

/**
 * Contains simple CRUD tests for simple resources
 */
trait SimpleCRUDResourceTests
{
    use FormatsData,
        CRUDResourceTestable;

    /** @test */
    public function it_creates_a_new_resource()
    {
        if (!$this->shouldTestOperation('create')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD create operations");
        }

        // setup application before test
        $this->setUpApplicationForTestOn('create');

        // call application
        $this->json('POST', "/api/{$this->getCRUDResource()}", $input = $this->getInputData())
            ->assertStatus(201)
            ->assertJsonFragment($this->getResponseData($input));

        // assert the resource was stored to database
        $this->assertDatabaseHas($this->getCRUDTable(), $this->getDatabaseData($input));

    }

    /** @test */
    public function it_deletes_the_resource()
    {
        if (!$this->shouldTestOperation('delete')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD delete operations");
        }

        // setup application before test
        $this->setUpApplicationForTestOn('delete');
        $model = $this->getExistingResource();

        // call application
        $this->json('DELETE', "/api/{$this->getCRUDResource()}/{$model->id}")
            ->assertSuccessful();

        if($this->resourceHasSoftDelete()){
            $this->assertSoftDeleted($this->getCRUDTable(), $this->getDatabaseMissingDataAfterDelete($model));
        } else {
            $this->assertDatabaseMissing($this->getCRUDTable(), $this->getDatabaseMissingDataAfterDelete($model));
        }
    }

    /** @test */
    public function it_returns_the_resource()
    {
        if (!$this->shouldTestOperation('read')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD read operations");
        }

        // setup application before test
        $this->setUpApplicationForTestOn('read');
        $id = with($model = $this->getExistingResource())->id;

        // call application
        $this->json('GET', "/api/{$this->getCRUDResource()}/{$id}")
            ->assertSuccessful()
            ->assertJson($model->toArray());
    }

    /** @test */
    public function it_list_the_resource()
    {
        if (!$this->shouldTestOperation('read')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD read operations");
        }

        // setup application before test
        $this->setUpApplicationForTestOn('read');
        $models = Collection::times(20, function () {
            return $this->getExistingResource();
        });

        $this->json('GET', "/api/{$this->getCRUDResource()}")
            ->assertSuccessful()
            ->assertJsonPagination(
                $models->forPage(1,15)->toArray(),
                20
            );
    }

    /** @test */
    public function it_updates_the_resource()
    {
        if (!$this->shouldTestOperation('update')) {
            $this->markTestSkipped("{static::class} is configured to not test CRUD update operations");
        }

        // setup application before test
        $this->setUpApplicationForTestOn('update');
        $id = with($model = $this->getExistingResource())->id;

        $this->json('PUT', "/api/{$this->getCRUDResource()}/{$id}", with($input = $this->getInputData()))
            ->assertSuccessful()
            ->assertJsonFragment($this->getResponseData($input));

        // assert the resource was updated to database
        $this->assertDatabaseHas(
            $this->getCRUDTable(),
            array_merge(['id' => $id], $this->getDatabaseDataAfterUpdate($input, $model))
        );
    }
}
