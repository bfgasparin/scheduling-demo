<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Global "To" cellphone
    |--------------------------------------------------------------------------
    |
    | Global cellphone number to be used on integration tests that needs to
    | send sms messages to external services. Here, you may specify a cellphone
    | number that is used globally on all sms messages that are sent by
    | the application during the integration tests.
    |
    */
    'to' => env('SMS_TO', '11952552021'),
];
