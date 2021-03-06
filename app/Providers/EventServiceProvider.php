<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
      'App\Events\IntakeAvailableEvent' => [
        'App\Listeners\IntakeAvailableListener',
      ],
      'App\Events\IntakeConfirmEvent' => [
        'App\Listeners\IntakeConfirmListener',
      ],
      'App\Events\ContentCRUDEvent' => [
        'App\Listeners\ContentCRUDListener',
      ],
      'App\Events\PushNotificationEvent' => [
        'App\Listeners\PushNotificationListener',
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
