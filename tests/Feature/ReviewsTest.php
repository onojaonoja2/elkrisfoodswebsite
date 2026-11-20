<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReviewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_creates_pending_review(): void
    {
        $product = Product::factory()->create(['is_published' => true]);

        $response = $this->post(route('reviews.store', $product->slug), [
            'name' => 'Chidi Okafor',
            'email' => 'chidi@example.com',
            'rating' => 5,
            'body' => 'Great taste and my family loves it.',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'name' => 'Chidi Okafor',
            'rating' => 5,
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_pending_review_is_not_public_but_approved_is(): void
    {
        $product = Product::factory()->create(['is_published' => true]);
        Review::factory()->pending()->create(['product_id' => $product->id, 'name' => 'Hidden Review']);
        Review::factory()->create([
            'product_id' => $product->id,
            'name' => 'Visible Review',
            'rating' => 4,
        ]);

        $this->get(route('products.show', $product->slug))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('products/show')
                ->has('reviews', 1)
                ->where('reviews.0.name', 'Visible Review')
                ->where('ratingSummary.count', 1)
                ->where('ratingSummary.average', fn ($value) => $value == 4.0));
    }

    public function test_store_validates_rating_bounds_and_required_fields(): void
    {
        $product = Product::factory()->create(['is_published' => true]);

        $response = $this->post(route('reviews.store', $product->slug), [
            'name' => '',
            'email' => 'not-an-email',
            'rating' => 9,
            'body' => '',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'rating', 'body']);
        $this->assertSame(0, Review::query()->count());
    }

    public function test_store_returns_404_for_unpublished_product(): void
    {
        $product = Product::factory()->create(['is_published' => false]);

        $this->post(route('reviews.store', $product->slug), [
            'name' => 'A',
            'email' => 'a@example.com',
            'rating' => 5,
            'body' => 'Nice.',
        ])->assertNotFound();
    }

    public function test_guest_review_is_stored_without_user(): void
    {
        $product = Product::factory()->create(['is_published' => true]);

        $this->post(route('reviews.store', $product->slug), [
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'rating' => 3,
            'body' => 'Decent product.',
        ]);

        $this->assertDatabaseHas('reviews', [
            'product_id' => $product->id,
            'user_id' => null,
            'status' => Review::STATUS_PENDING,
        ]);
    }
}
