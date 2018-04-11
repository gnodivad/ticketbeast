<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class ConcertAdded
{
    public $concert;

    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct($concert)
    {
        $this->concert = $concert;
    }
}
