<?php

namespace Tests\Feature\API\Concerns\Validation;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

/**
 * Contains tests related with phone field validation error
 *
 * @see hhttps://github.com/googlei18n/libphonenumber
 */
trait PhoneRuleTests
{
    /**
     * @dataProvider phoneDataProviderForCreate
     * @test
     */
    public function it_validates_phone_rule_on_create(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnCreate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }

    /**
     * @dataProvider phoneDataProviderForUpdate
     * @test
     */
    public function it_validates_phone_rule_on_update(string $attribute, $value) : void
    {
        $this->expectsValidationErrorsOnUpdate(
            $this->makeInputDataUsingAttribute($attribute, $value),
            [$attribute]
        );
    }
    // providers

    public function phoneDataProviderForCreate()
    {
        return $this->phoneDataProvider(
            $this->getRules($this->phoneRules, 'create')
        );
    }

    public function phoneDataProviderForUpdate()
    {
        return $this->phoneDataProvider(
            $this->getRules($this->phoneRules, 'update')
        );
    }

    // providers

    protected function phoneDataProvider(array $rules) : array
    {
        $mobileRules = $this->getRuleTestDataForMobile($rules);
        $fixedLineRules = $this->getRuleTestDataForLandLine($rules);

        return $mobileRules->merge($fixedLineRules)->toArray();
    }

    protected function getRuleTestDataForMobile($rules) : Collection
    {
        return collect($rules['mobile'] ?? [])->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'invalidcellphone'],
                [$rule, 'abcdefghijk'],
                [$rule, 'abcdefghij'],
                [$rule, 'a1952187626'],
                [$rule, 'a195218762'],
                [$rule, '286438h283'],
                [$rule, '286438h283w'],
                [$rule, '119999090981'],
            ];
        })->collapse();
    }

    protected function getRuleTestDataForLandLine($rules) : Collection
    {
        return collect($rules['landline'] ?? [])->map(function ($rule) {
            // return the test parameters
            return [
                [$rule, 'invalidcellphone'],
                [$rule, 'abcdefghijk'],
                [$rule, 'abcdefghij'],
                [$rule, 'a195218762'],
                [$rule, '286438h283'],
            ];
        })->collapse();
    }
}
