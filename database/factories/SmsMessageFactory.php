<?php

/*
|--------------------------------------------------------------------------
| SmsMessage Factories
|--------------------------------------------------------------------------
|
| SmsMessage factories give a convenient way to create models for testing and
| seeding the database. Here we tell the factory how the smsMessage model
| should look.
|
| @see App\SmsMessage
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\SmsMessage::class, function (Faker\Generator $faker) { return [
        'id' => $faker->uuid,
        'recipient_id' => factory(App\User::class),
        'recipient_type' => App\User::class,
        'channel' => str_random(5),
        'channel_response' => str_random(100),
    ];
})->state(App\SmsMessage::class, 'nexmo', function ($faker) {
    $messageId = strtoupper($faker->bothify('#?#######?###???'));

    return [
        'channel' => 'nexmo',
        'channel_id' => $messageId,
        'channel_response' => [
            'messages' =>  [
                [
                    'to'=> '55'.$faker->cellphoneNumber(false),
                    'status'=> '0',
                    'network'=> '72416', // TODO faker para mobile country code (https://en.wikipedia.org/wiki/Mobile_country_code)
                    'message-id'=> $messageId,
                    'message-price'=> $faker->randomFloat(8, 0, 1),
                    'remaining-balance'=> $faker->randomFloat(8, 0, 10)
                ]
            ],
            'message-count'=> '1',
        ],
    ];
})->state(App\SmsMessage::class, 'nexmo_delivered', function ($faker) {
    $messageId = strtoupper($faker->bothify('#?#######?###???'));
    $cellphone = '55'.$faker->cellphoneNumber(false);
    $messagePrice = $faker->randomFloat(8, 0, 1);
    $balance = $faker->randomFloat(8, 0, 10);
    $deliveredDate = $faker->dateTime;

    return [
        'channel' => 'nexmo',
        'channel_id' => $messageId,
        'channel_response' => [
            'messages' =>  [
                [
                    'to'=> $cellphone,
                    'status'=> '0',
                    'network'=> '72416', // TODO faker para mobile country code (https://en.wikipedia.org/wiki/Mobile_country_code)
                    'message-id'=> $messageId,
                    'message-price'=> $messagePrice,
                    'remaining-balance'=> $balance,
                ]
            ],
            'message-count'=> '1'
        ],
        'delivery_receipt' => [
            'msisdn' => $cellphone,
            'to' => $cellphone,
            'network-code' => '72406', // TODO faker para mobile country code (https://en.wikipedia.org/wiki/Mobile_country_code)
            'messageId' => $messageId,
            'price' => $messagePrice,
            'status' => 'delivered',
            'scts' => '1706142159',
            'err-code' => '0',
            'message-timestamp' => $deliveredDate->format('Y-m-d H:i:s'),
        ],
        'delivered_at' => $deliveredDate,
    ];
});
