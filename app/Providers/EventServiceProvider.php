<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Salon\Client\Booking\Creating' => [
            'App\Listeners\Salon\Client\Booking\BackupService',
        ],
        'App\Events\Salon\Client\Booking\Updating' => [
            'App\Listeners\Salon\Client\Booking\DenyChanges',
        ],
        'Illuminate\Notifications\Events\NotificationSent' => [
            'App\Listeners\SmsMessage\CreateFromNotification',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
