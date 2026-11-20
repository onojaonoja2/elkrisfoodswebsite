<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function show(string $number): Response
    {
        $order = Order::query()
            ->where('number', $number)
            ->with('items')
            ->firstOrFail();

        return Inertia::render('orders/show', [
            'order' => $order,
            'waUrl' => $order->waUrl(),
        ]);
    }
}
