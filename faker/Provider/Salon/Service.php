<?php

namespace Faker\Provider\Salon;

use Faker\Provider\Base as BaseProvider;

/**
 * Provides salon related fake data
 * for Faker Generator
 *
 * @see Faker\Generator
 * @see Faker\Provider\Base
 */
class Service extends BaseProvider
{
    protected static $names = [
        "Men's cut",
        "Women Cut (Short)",
        "Women Cut (Medium)",
        "Men's cut (Long)",
        "Women Cut (Long)",
        "Child Cut",
        "Hair Dye (Short)",
        "Hair Dye (Medium)",
        "Hair Dye (Long)",
        "Lymphatic Drainage",
        "Eyebrows (cleaning)",
        "Eyebrows (in-between)",
        "Depilation (Back)",
        "Depilation (Thigh)",
        "Depilation (Buttocks)",
        "Depilation (Full Leg)",
        "Depilation (Shoulder)",
        "Depilation (Groin)",
        "Massage (Sport)",
        "Relaxing Massage",
        "Quick Massage",
        "Hair Highlights (Short)",
        "Hair Highlights (Medium)",
        "Hair Highlights (Long)",
        "Hair Moisturizing",
        "Manicure",
        "Pedicure",
        "Modeling Brush",
        "Hair Straightening",
        "Brazilian blowout",
        "Hair Coloring",
        "Cauterization",
        "Gel Nail Replenishment",
        "Hair Streaking",
        "Make up",
        "Peeling",
        "Shiatsu",
        "Yoga",
        "Hair Styling",
        "Hand SPA",
        "Feet SPA",
        "Acne Phototherapy",
        "Dark Circles Phototherapy",
    ];

    /**
     * Get a salon service name
     *
     * @example Mair Moisturizing
     */
    public static function salonServiceName()
    {
        return static::randomElement(static::$names);
    }
}
