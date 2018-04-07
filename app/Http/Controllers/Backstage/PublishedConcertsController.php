<?php

namespace App\Http\Controllers\Backstage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Concert;

class PublishedConcertsController extends Controller
{
    public function store()
    {
        $concert = Concert::find(request('concert_id'));

        if ($concert->isPublished()) {
            abort(422);
        }

        $concert->publish();
        return redirect()->route('backstage.concerts.index');
    }
}
