<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AccountOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/account')->assertRedirect('/login');
    }

    public function test_user_sees_only_their_own_orders(): void
    {
        $user = User::factory()->create();
        $mine = Order::factory()->create(['user_id' => $user->id]);
        Order::factory()->create();

        $this->actingAs($user)
            ->get('/account')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('account/orders')
                ->has('orders', 1)
                ->where('orders.0.number', $mine->number));
    }

    public function test_user_can_view_their_own_order_detail(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('account.orders', $order->number))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/show')
                ->where('order.number', $order->number)
                ->where('fromAccount', true));
    }

    public function test_user_cannot_view_another_users_order_via_account(): void
    {
        $user = User::factory()->create();
        $someoneElses = Order::factory()->create();

        $this->actingAs($user)
            ->get(route('account.orders', $someoneElses->number))
            ->assertNotFound();
    }
}
