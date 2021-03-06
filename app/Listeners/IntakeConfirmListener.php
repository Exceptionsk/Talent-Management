<?php

namespace App\Listeners;

use App\Events\IntakeConfirmEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\Confirmation;

class IntakeConfirmListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  IntakeConfirmEvent  $event
     * @return void
     */
    public function handle(IntakeConfirmEvent $event)
    {
        Mail::to($event->email)->send(new Confirmation($event->qr));
    }
}
