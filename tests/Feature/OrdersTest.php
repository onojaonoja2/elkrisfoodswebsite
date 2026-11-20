<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_order_tracking_page_without_prices(): void
    {
        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => Product::factory()->create()->id,
            'variant_id' => ProductVariant::factory()->create()->id,
            'product_name' => 'Elkris Oat Flour',
            'size_label' => '1.3kg',
            'pack_type' => 'single',
            'unit_price' => 3980,
            'quantity' => 2,
            'line_total' => 7960,
        ]);

        $this->get(route('orders.show', $order->number))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('orders/show')
                ->where('order.number', $order->number)
                ->where('order.status', 'pending')
                ->has('order.items', 1)
                ->has('waUrl'));
    }

    public function test_whatsapp_message_does_not_include_prices(): void
    {
        Setting::set('contact.whatsapp', '2348012345678');

        $order = Order::factory()->create();
        $order->items()->create([
            'product_id' => Product::factory()->create()->id,
            'variant_id' => ProductVariant::factory()->create()->id,
            'product_name' => 'Elkris Oat Flour',
            'size_label' => '1.3kg',
            'pack_type' => 'single',
            'unit_price' => 3980,
            'quantity' => 2,
            'line_total' => 7960,
        ]);

        $message = urldecode($order->waUrl());

        $this->assertStringContainsString('Elkris Oat Flour', $message);
        $this->assertStringNotContainsString('₦', $message);
        $this->assertStringNotContainsString('Subtotal', $message);
    }

    public function test_show_returns_404_for_unknown_reference(): void
    {
        $this->get('/orders/ELK-000000')->assertNotFound();
    }
}
