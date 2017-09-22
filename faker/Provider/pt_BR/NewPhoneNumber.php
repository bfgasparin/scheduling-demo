<?php

namespace Faker\Provider\pt_BR;

use Faker\Provider\pt_BR\PhoneNumber as BasePhoneNumber;

/**
 * Temp Provider to fix areaCode provider from pt_BR PhoneNumber provider
 * TODO fix pt_BR PhoneNumber provider and push to the official github repo
 *
 * @see Faker\Provider\pt_BR\PhoneNumber
 */
class NewPhoneNumber extends BasePhoneNumber
{
    /**
     * Extracted from http://ddd.online24hs.com.br
     */
    protected static $areaCode = [
        11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 22, 24, 27,
        28, 31, 32, 33, 34, 35, 37, 38, 41, 42, 43, 44, 45,
        46, 47, 48, 49, 51, 53, 54, 55, 61, 62, 63, 64, 65,
        66, 67, 68, 69, 71, 73, 74, 75, 77, 79, 81, 82, 83,
        84, 85, 86, 87, 88, 89, 91, 92, 93, 94, 95, 96, 97,
        98, 99,
    ];

    /**
     * Generates a 2-digit area code not composed by zeroes.
     * @return string
     */
    public static function areaCode()
    {
        return static::randomElement(static::$areaCode);
    }
}
