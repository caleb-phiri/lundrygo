<?php

namespace App\Listeners;

use App\Events\OrderDelivered;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFeedbackRequest
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderDelivered $event): void
    {
        //
    }
}
