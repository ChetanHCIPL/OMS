<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
       /* Registered::class => [
            SendEmailVerificationNotification::class,
        ],*/
         \App\Events\NewBuyerRegistered::class => [
            \App\Listeners\SendBuyerWelcomeEmail::class,
        ],
         \App\Events\MemberNotification::class => [
            \App\Listeners\MemberSendNotification::class,
        ],
         \App\Events\SendTopicNotification::class => [
            \App\Listeners\SendTopicNotificationListner::class,
        ],

        \App\Events\SendBirthdayNotification::class => [
            \App\Listeners\SendBirthdayNotificationListner::class,
        ],

        \App\Events\SendOtpNotification::class => [
            \App\Listeners\SendOtpNotificationListner::class,
        ],

        \App\Events\ResendOtpNotification::class => [
            \App\Listeners\ResendOtpNotificationListner::class,
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
