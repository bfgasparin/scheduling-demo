<?php

namespace Tests\Feature\API\Concerns\Validation;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Contains tests related with date_format field vali
 * dation error
 *
 * @see https://laravel.com/docs/validation#rule-date-format
 */
trait DateFormatRuleTests
{
    /**
     * @dataProvider dateFormatDataProviderForCreate
     * @test
     */
    public function it_validate_date_format_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider dateFormatDataProviderForUpdate
     * @test
     */
    public function it_validates_date_format_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function dateFormatDataProviderForCreate()
    {
        return $this->dateFormatDataProvider(
            $this->getRules($this->dateFormatRules, 'create')
        );
    }

    public function dateFormatDataProviderForUpdate()
    {
        return $this->dateFormatDataProvider(
            $this->getRules($this->dateFormatRules, 'update')
        );
    }

    // providers

    protected function dateFormatDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            $attribute = with(is_array($rule) ? $rule[0] : $rule); // attribute name

            switch (with(is_array($rule) ? ($rule[1] ?? 'Y-m-d') : 'Y-m-d')) {
                // TODO create data for more use cases
                case 'H:i:s':
                    $parameters = $this->getTestDataForTimeFormat($attribute);
                    break;
                default:
                    $parameters = $this->getTestDataForDateFormat($attribute);
            }

            // return the test parameters
            return array_merge($parameters, [
                [$attribute, ''],
                [$attribute, null],
                [$attribute, 'Some Date'],
                [$attribute, '40/03/2016'],
                [$attribute, '01/40/2016'],
                [$attribute, '01/40/2016'],
                [$attribute, 9],
                [$attribute, 287463],
                [$attribute, 433.1265],
            ]);
        })->collapse()->toArray();
    }

    protected function getTestDataForTimeFormat(string $attribute) : array
    {
        return [
            [$attribute, Carbon::now()->format('d/m/Y')],
            [$attribute, Carbon::now()->toAtomString()],
            [$attribute, Carbon::now()->toCookieString()],
            [$attribute, Carbon::now()->toDateString()],
            [$attribute, Carbon::now()->toDateTimeString()],
            [$attribute, Carbon::now()->toDayDateTimeString()],
            [$attribute, Carbon::now()->toFormattedDateString()],
            [$attribute, Carbon::now()->toIso8601String()],
            [$attribute, Carbon::now()->toRfc1036String()],
            [$attribute, Carbon::now()->toRfc1123String()],
            [$attribute, Carbon::now()->toRfc2822String()],
            [$attribute, Carbon::now()->toRfc3339String()],
            [$attribute, Carbon::now()->toRfc822String()],
            [$attribute, Carbon::now()->toRfc850String()],
            [$attribute, Carbon::now()->toRssString()],
            [$attribute, Carbon::now()->toW3cString()],
        ];
    }

    protected function getTestDataForDateFormat(string $attribute) : array
    {
        return [
            [$attribute, Carbon::now()->toAtomString()],
            [$attribute, Carbon::now()->toCookieString()],
            [$attribute, Carbon::now()->toDateTimeString()],
            [$attribute, Carbon::now()->toDayDateTimeString()],
            [$attribute, Carbon::now()->toFormattedDateString()],
            [$attribute, Carbon::now()->toIso8601String()],
            [$attribute, Carbon::now()->toRfc1036String()],
            [$attribute, Carbon::now()->toRfc1123String()],
            [$attribute, Carbon::now()->toRfc2822String()],
            [$attribute, Carbon::now()->toRfc3339String()],
            [$attribute, Carbon::now()->toRfc822String()],
            [$attribute, Carbon::now()->toRfc850String()],
            [$attribute, Carbon::now()->toRssString()],
            [$attribute, Carbon::now()->toTimeString()],
            [$attribute, Carbon::now()->toW3cString()],
        ];
    }

}
