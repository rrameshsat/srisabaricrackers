<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyOrderCreated implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function handle()
    {
        // Placeholder: implement notification, email, or logging here
        // Example:
        // Log::info("Order created: {$this->orderId}");
    }
}
