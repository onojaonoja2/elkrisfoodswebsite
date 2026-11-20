<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('account/orders', [
            'orders' => Order::query()
                ->where('user_id', $request->user()->id)
                ->with('items')
                ->latest()
                ->get(),
        ]);
    }

    public function order(Request $request, string $number): Response
    {
        $order = Order::query()
            ->where('number', $number)
            ->where('user_id', $request->user()->id)
            ->with('items')
            ->firstOrFail();

        return Inertia::render('orders/show', [
            'order' => $order,
            'waUrl' => $order->waUrl(),
            'fromAccount' => true,
        ]);
    }
}
