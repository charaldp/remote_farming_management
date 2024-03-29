<?php

namespace App\Providers;

use App\Events\ControlDeviceSwitchedIsOnStatus;
use App\Events\WateringEntryAdded;
use App\Listeners\CreateWateringEntry;
use App\Listeners\SetupControlDeviceTurningOff;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ControlDeviceSwitchedIsOnStatus::class => [
            CreateWateringEntry::class,
        ],
        WateringEntryAdded::class => [
            SetupControlDeviceTurningOff::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
