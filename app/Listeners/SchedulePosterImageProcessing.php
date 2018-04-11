<?php

namespace App\Listeners;

use App\Jobs\ProcessPosterImage;

class SchedulePosterImageProcessing
{
    public function handle($event)
    {
        if ($event->concert->hasPoster()) {
            ProcessPosterImage::dispatch($event->concert);
        }
    }
}
