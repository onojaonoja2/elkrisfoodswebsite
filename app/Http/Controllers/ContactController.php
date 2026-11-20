<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaConversionEvent;
use App\Models\ContactLead;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    private const INTENTS = [
        'order',
        'retailer',
        'product_info',
        'complaint',
        'distributor',
        'partner',
    ];

    public function index(): Response
    {
        return Inertia::render('contact', [
            'settings' => Setting::publicMap(),
            'intents' => self::INTENTS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'intent' => ['required', Rule::in(self::INTENTS)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactLead::create($validated);

        SendMetaConversionEvent::dispatch('Lead', [
            'content_name' => 'Contact Form',
            'content_category' => $validated['intent'],
        ], [
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ], $request->fullUrl());

        return back()->with('success', 'Thank you! The Elkris Foods team will get back to you shortly.');
    }
}
