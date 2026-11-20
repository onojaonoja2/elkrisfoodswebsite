<?php

namespace Tests\Feature;

use App\Filament\Pages\SiteContent;
use App\Models\ContactLead;
use App\Models\DistributorLead;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminContentTest extends TestCase
{
    use RefreshDatabase;

    protected function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_non_admin_user_cannot_view_the_admin_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/products')
            ->assertForbidden();
    }

    public function test_product_resource_index_requires_auth(): void
    {
        $this->get('/admin/products')->assertRedirect();
    }

    public function test_authenticated_user_can_view_products_resource(): void
    {
        Product::factory()->create();

        $this->actingAs($this->admin())
            ->get('/admin/products')
            ->assertOk();
    }

    public function test_authenticated_user_can_view_contact_leads_resource(): void
    {
        ContactLead::factory()->create();

        $this->actingAs($this->admin())
            ->get('/admin/contact-leads')
            ->assertOk();
    }

    public function test_authenticated_user_can_view_distributor_leads_resource(): void
    {
        DistributorLead::factory()->create();

        $this->actingAs($this->admin())
            ->get('/admin/distributor-leads')
            ->assertOk();
    }

    public function test_settings_page_requires_auth_and_renders(): void
    {
        $this->get('/admin/settings')->assertRedirect();

        $this->actingAs($this->admin())
            ->get('/admin/settings')
            ->assertOk();
    }

    public function test_site_content_page_requires_auth_and_renders(): void
    {
        $this->get('/admin/site-content')->assertRedirect();

        $this->actingAs($this->admin())
            ->get('/admin/site-content')
            ->assertOk();
    }

    public function test_admin_can_save_about_content_from_site_content_page(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(SiteContent::class)
            ->fillForm([
                'about_body.who_we_are' => [
                    ['paragraph' => 'First paragraph.'],
                ],
                'about_body.vision' => 'Saved vision.',
                'about_body.mission' => 'Saved mission.',
                'about_body.values_intro' => 'Saved intro.',
                'about_body.core_values' => [
                    ['title' => 'Care', 'icon' => 'Heart', 'description' => 'We care.'],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $about = Setting::aboutContent();

        $this->assertSame('Saved vision.', $about['vision']);
        $this->assertSame(['First paragraph.'], $about['who_we_are']);
        $this->assertSame('Care', $about['core_values'][0]['title']);
    }

    public function test_admin_can_save_new_public_content_fields(): void
    {
        $this->actingAs($this->admin());

        Livewire::test(SiteContent::class)
            ->fillForm([
                'search.submit' => 'Find it',
                'gallery.intro_count' => 'Showing {count} memories',
                'contact.intents.order' => 'Place an order',
                'distributor.business_types.retailer' => 'Retail shop',
                'distributor.monthly_orders.1_5' => '1-5 cartons',
                'distributor.required_hint' => 'required',
                'orders.whatsapp_cta_continue' => 'Chat to continue',
                'orders.quantity_times' => '{quantity} of {size}',
                'global.whatsapp.order_message' => 'Hello {product} x{quantity}',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Find it', Setting::content('search')['submit']);
        $this->assertSame('Showing {count} memories', Setting::content('gallery')['intro_count']);
        $this->assertSame('Place an order', Setting::content('contact')['intents']['order']);
        $this->assertSame('Retail shop', Setting::content('distributor')['business_types']['retailer']);
        $this->assertSame('1-5 cartons', Setting::content('distributor')['monthly_orders']['1_5']);
        $this->assertSame('required', Setting::content('distributor')['required_hint']);
        $this->assertSame('Chat to continue', Setting::content('orders')['whatsapp_cta_continue']);
        $this->assertSame('{quantity} of {size}', Setting::content('orders')['quantity_times']);
        $this->assertSame('Hello {product} x{quantity}', Setting::content('global')['whatsapp']['order_message']);
    }
}
