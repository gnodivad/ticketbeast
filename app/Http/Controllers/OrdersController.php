<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;

class OrdersController extends Controller
{
    public function show($confirmationNumber)
    {
        $order = Order::where('confirmation_number', $confirmationNumber)->first();
         
        return view('orders.show', ['order' => $order]);
    }
}
