<?php

namespace Tests\Feature\API\Concerns\Validation;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Contains tests related with date field validation error
 *
 * @see https://laravel.com/docs/validation#rule-date
 */
trait DateRuleTests
{
    /**
     * @dataProvider dateDataProviderForCreate
     * @test
     */
    public function it_validates_date_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider dateDataProviderForUpdate
     * @test
     */
    public function it_validates_date_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function dateDataProviderForCreate()
    {
        return $this->dateDataProvider(
            $this->getRules($this->dateRules, 'create')
        );
    }

    public function dateDataProviderForUpdate()
    {
        return $this->dateDataProvider(
            $this->getRules($this->dateRules, 'update')
        );
    }

    // providers

    protected function dateDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, ''],
                [$rule, null],
                [$rule, 'Some Date'],
                [$rule, '40/03/2017'],
                [$rule, '01/40/2017'],
                [$rule, '2017/03/40'],
                [$rule, '2017/40/01'],
                [$rule, '2017-30-01'],
                [$rule, '2017-05-50'],
                [$rule, '32-03-2017'],
                [$rule, '01-13-2017'],
                [$rule, Carbon::now()->toTimeString()],
                [$rule, 9],
                [$rule, 287463],
                [$rule, 433.1265],

            ];
        })->collapse()->toArray();
    }
}
