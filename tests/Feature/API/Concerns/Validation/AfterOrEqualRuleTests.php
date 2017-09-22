<?php

namespace Tests\Feature\API\Concerns\Validation;

use Carbon\Carbon;
use RuntimeException;
use Illuminate\Support\Str;

/**
 * Contains tests related with date_format field vali
 * dation error
 *
 * @see https://laravel.com/docs/validation#rule-date-format
 */
trait AfterOrEqualRuleTests
{
    /**
     * @dataProvider afterOrEqualDataProviderForCreate
     * @test
     */
    public function it_validate_after_or_equal_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider afterOrEqualDataProviderForUpdate
     * @test
     */
    public function it_validates_after_or_equal_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    // providers

    public function afterOrEqualDataProviderForCreate()
    {
        return $this->afterOrEqualDataProvider(
            $this->getRules($this->afterOrEqualRules, 'create')
        );
    }

    public function afterOrEqualDataProviderForUpdate()
    {
        return $this->afterOrEqualDataProvider(
            $this->getRules($this->afterOrEqualRules, 'update')
        );
    }

    // providers

    protected function afterOrEqualDataProvider(array $rules) : array
    {
        return collect($rules)->map(function ($rule) {
            if( ! is_array($rule) || ! isset($rule[1]) ) {
                throw new RuntimeException(
                    'To test after_or_equal validation rule, you must set a parameter to test against:          ' . PHP_EOL . PHP_EOL .
                    '  /**                                                                                      ' . PHP_EOL .
                    '  * The fields that should be tested against the after_or_equal validation rule            ' . PHP_EOL .
                    '  *                                                                                        ' . PHP_EOL .
                    '  * @see AfterOrEqualRuleTests                                                             ' . PHP_EOL .
                    '  */                                                                                       ' . PHP_EOL .
                    "  protected \$afterOrEqualRules = [                                                        " . PHP_EOL .
                    "      ['field', 'tomorrow']                                                                " . PHP_EOL .
                    "  ];                                                                                       "
                );
            }
            $attribute = $rule[0]; // attribute name
            $value = Carbon::parse($rule[1]); // value

            // return the test parameters
            return [
                [$attribute, $value->subSecond()],
                [$attribute, $value->subSeconds(rand(1,10))],
                [$attribute, $value->subMinute()],
                [$attribute, $value->subMinutes(rand(1,10))],
                [$attribute, $value->subHour()],
                [$attribute, $value->subHours(rand(1,10))],
                [$attribute, $value->subDay()],
                [$attribute, $value->subDays(rand(1,10))],
                [$attribute, $value->subWeek()],
                [$attribute, $value->subWeeks(rand(1,10))],
                [$attribute, $value->subQuarter()],
                [$attribute, $value->subQuarters(rand(1,10))],
                [$attribute, $value->subMonth()],
                [$attribute, $value->subMonths(rand(1,10))],
                [$attribute, $value->subYear()],
                [$attribute, $value->subYears(rand(1,10))],
            ];
        })->collapse()->toArray();
    }
}
