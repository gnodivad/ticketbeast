<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Concert;

class ConcertOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store($concertId)
    {
        $concert = Concert::find($concertId);
        $ticketQuanlity = request('ticket_quantity');
        $amount = $ticketQuanlity * $concert->ticket_price;
        $token = request('token');
        $this->paymentGateway->charge($amount, $token);

        $order = $concert->orders()->create(['email' => request('email')]);

        foreach (range(1, $ticketQuanlity) as $i) {
            $order->tickets()->create([]);
        }

        return response()->json([], 201);
    }
}
