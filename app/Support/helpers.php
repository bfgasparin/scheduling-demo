<?php

if (! function_exists('sms_token')) {
    /**
     * Generate a reliable 4 length alpha-numeric random string
     * to be used as sms token into the application
     *
     * @return string
     */
    function sms_token()
    {
        return str_random(4);
    }
}

if (! function_exists('date_random')) {
    /**
     * Generate a random Carbon instance
     *
     * @return Carbon\Carbon
     */
    function date_random() : Carbon\Carbon
    {
        return Carbon\Carbon::today()->addDay(rand(-1000, 1000));
    }
}

if (! function_exists('date_random_between')) {
    /**
     * Generate a random Carbon instance between the given range of dates using the given interval
     * between the ranged dates
     *
     * @param  \Carbon\Carbon  $begin
     * @param  \Carbon\Carbon  $end
     * @param  bool  $inclusive
     * @param  \Carbon\CarbonInterval  $interval
     *
     * @return Carbon\carbon
     */
    function date_random_between(Carbon\Carbon $begin, Carbon\Carbon $end, bool $inclusive = true, Carbon\CarbonInterval $interval = null) : Carbon\Carbon
    {
        return collect(date_range($begin, $end, $inclusive, $interval))->random();
    }
}

if (! function_exists('date_range')) {
    /**
     * Compute a range between two dates, and generate
     * a plain array of Carbon objects of each day in it
     *
     * You can customize the interval between the ranged dates passing the
     * interval parameter. Default interval is one day
     * between them.
     *
     * If no interval is set
     *
     * @param  \Carbon\Carbon  $from
     * @param  \Carbon\Carbon  $to
     * @param  bool  $inclusive
     * @param  \Carbon\CarbonInterval  $interval
     *
     * @return array|null
     */
    function date_range(Carbon\Carbon $from, Carbon\Carbon $to, bool $inclusive = true, Carbon\CarbonInterval $interval = null) : ?array
    {
        if ($from->gt($to)) {
            return null;
        }

        $step = $interval ?? Carbon\CarbonInterval::day();

        // Clone the date objects to avoid issues, then reset their time
        // if the inverval is equal or greater than one day
        $from = $from->copy();
        $to = $to->copy();
        if ($step->dayz > 0 || $step->weeks > 0 || $step->months > 0 || $step->years > 0) {
            $from->startOfDay();
            $to->startOfDay();
        }

        // Include the end date in the range
        if ($inclusive) {
            $to->addDay();
        }

        $period = new DatePeriod($from, $step, $to);

        // Convert the DatePeriod into a plain array of Carbon objects
        $range = [];

        foreach ($period as $day) {
            $range[] = new Carbon\Carbon($day);
        }

        return ! empty($range) ? $range : null;
    }
}

if (! function_exists('time_range')) {
    /**
     * Compute a range between two times, and generate
     * a plain array of times with the given interval in minutes between them
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int $interval  Interval in minutes
     *
     * @return array|null
     */
    function time_range(string $from, string $to, int $interval) : ?array
    {
        return collect(date_range(
            Carbon\Carbon::parse($from),
            Carbon\Carbon::parse($to),
            false,
            Carbon\CarbonInterval::minutes($interval)
        ))->map->toTimeString()->toArray();
    }
}

if (! function_exists('time_random')) {
    /**
     * Generate a random time string
     *
     * @return string
     */
    function time_random() : string
    {
        return Carbon\Carbon::today()->addMinutes(rand(1, 1439))->toTimeString();
    }
}

if (! function_exists('time_random_between')) {
    /**
     * Generate a random time string between the given range of times using the given interval
     * in minutes between the ranged times
     *
     * @param  string  $begin
     * @param  string  $end
     * @param  int $interval  Interval in minutes
     *
     * @return array|null
     */
    function time_random_between(string $begin, string $end, int $interval) : string
    {
        return collect(time_range($begin, $end, $interval))->random();
    }
}

if (! function_exists('time_min')) {
    /**
     * Find lowest time value
     *
     * @param  ...$times
     *
     * @return string the lowest time
     */
    function time_min(...$times) : string
    {
        $times = collect($times)->map(function ($time) {
            return Carbon\Carbon::parse($time);
        });

        return min(...$times)->toTimeString();
    }
}

if (! function_exists('time_max')) {
    /**
     * Find highest time value
     *
     * @param  ...$times
     *
     * @return string the lowest time
     */
    function time_max(...$times) : string
    {
        $times = collect($times)->map(function ($time) {
            return Carbon\Carbon::parse($time);
        });

        return max(...$times)->toTimeString();
    }
}

if (! function_exists('beat')) {
    /**
     * Call the given Closure with the given value a given amount of time then return the value.
     *
     * @param  mixed  $value
     * @param  int  $amount
     * @param  callable  $callback
     * @return mixed
     */
    function beat($value, int $amount, $callback)
    {
        do {
            $amount--;
            $callback($value);
        } while ($amount > 0);

        return $value;
    }
}

if (! function_exists('repeat')) {
    /**
     * Call the given Closure a given amount of time
     *
     * @param  int  $amount
     * @param  callable  $callback
     * @return void
     */
    function repeat(int $amount, $callback) : void
    {
        for ($i = 0; $i < $amount; $i++) {
            $callback($i);
        }
    }
}

if (! function_exists('strip_non_alphanumeric')) {

    /**
    * Remove all characters except letters and numbers.
    *
    * @param string $string
    * @return string
    */
    function strip_non_alphanumeric($string)
    {
        return preg_replace("/[^a-z0-9]/i", "", $string);
    }
}
