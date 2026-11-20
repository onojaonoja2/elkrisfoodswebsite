<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaConversionEvent;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        Subscriber::query()->updateOrCreate(
            ['email' => strtolower($validated['email'])],
            [
                'name' => $validated['name'] ?? null,
                'source' => $validated['source'] ?? 'footer',
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        SendMetaConversionEvent::dispatch('Lead', [
            'content_name' => 'Newsletter Signup',
            'content_category' => $validated['source'] ?? 'footer',
        ], [
            'email' => $validated['email'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ], $request->fullUrl());

        return back()->with('success', 'You are on the list! Watch your inbox for recipes, product news and healthy-living tips from Elkris.');
    }
}
