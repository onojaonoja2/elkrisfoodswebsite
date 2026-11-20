<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_uses_default_content(): void
    {
        $this->get(route('about'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('about')
                ->where('about.vision', config('about.content.vision'))
                ->where('about.mission', config('about.content.mission'))
                ->has('about.who_we_are', 3)
                ->has('about.core_values', 6));
    }

    public function test_about_page_uses_saved_content_with_default_fallbacks(): void
    {
        Setting::set('about.content', json_encode([
            'vision' => 'An editable vision.',
        ]));

        $this->get(route('about'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('about.vision', 'An editable vision.')
                ->where('about.mission', config('about.content.mission')));
    }
}
