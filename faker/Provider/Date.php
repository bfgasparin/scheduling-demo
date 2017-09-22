<?php

namespace Faker\Provider;

use Faker\Provider\Base as BaseProvider;

/**
 * Date fake methods
 *
 * @see Faker\Generator
 * @see Faker\Provider\Base
 */
class Date extends BaseProvider
{

    /**
     * Get a date string based on a random date between two given dates.
     * Accepts date strings that can be recognized by strtotime().
     *
     * @param \DateTime|string $startDate Defaults to 30 years ago
     * @param \DateTime|string $endDate   Defaults to "now"
     * @param string $timezone time zone in which the date time should be set, default to result of `date_default_timezone_get`
     * @example DateTime('1999-02-02 11:42:52')
     * @return string
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateBetween($startDate = '-30 years', $endDate = 'now', $timezone = null) : string
    {
        return $this->generator->dateTimeBetween($startDate, $endDate, $timezone)->format('Y-m-d');
    }

    /**
     * Get a date string on a random date between one given date and
     * an interval
     * Accepts date string that can be recognized by strtotime().
     *
     * @param string $date      Defaults to 30 years ago
     * @param string $interval  Defaults to 5 days after
     * @param string $timezone time zone in which the date time should be set, default to result of `date_default_timezone_get`
     * @example dateTimeInInterval('1999-02-02 11:42:52', '+ 5 days')
     * @return string
     * @see http://php.net/manual/en/timezones.php
     * @see http://php.net/manual/en/function.date-default-timezone-get.php
     */
    public function dateInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
    {
        return $this->generator->dateTimeInInterval($date, $interval, $timezone)->format('Y-m-d');
    }

    /**
     * @param \DateTime|int|string $max maximum timestamp used as random end limit, default to "now"
     * @example DateTime('1964-04-04 11:02:02')
     * @return string
     */
    public function dateThisCentury($max = 'now')
    {
        return $this->generator->dateTimeBetween('-100 year', $max)->format('Y-m-d');
    }

    /**
     * @param \DateTime|int|string $max maximum timestamp used as random end limit, default to "now"
     * @example DateTime('2010-03-10 05:18:58')
     * @return string
     */
    public function dateThisDecade($max = 'now')
    {
        return $this->generator->dateTimeBetween('-10 year', $max)->format('Y-m-d');
    }

    /**
     * @param \DateTime|int|string $max maximum timestamp used as random end limit, default to "now"
     * @example DateTime('2011-09-19 09:24:37')
     * @return string
     */
    public function dateThisYear($max = 'now')
    {
        return $this->generator->dateTimeBetween('-1 year', $max)->format('Y-m-d');
    }

    /**
     * @param \DateTime|int|string $max maximum timestamp used as random end limit, default to "now"
     * @example DateTime('2011-10-05 12:51:46')
     * @return string
     */
    public function dateThisMonth($max = 'now')
    {
        return $this->generator->dateTimeBetween('-1 month', $max)->format('Y-m-d');
    }
}
