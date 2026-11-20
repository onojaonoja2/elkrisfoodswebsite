<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaConversionEvent;
use App\Models\DistributorLead;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DistributorController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('distributor', [
            'settings' => Setting::publicMap(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:255'],
            'state' => ['required', 'string', 'max:100'],
            'lga' => ['nullable', 'string', 'max:100'],
            'business_type' => ['nullable', 'string', 'max:100'],
            'monthly_order' => ['nullable', 'string', 'max:100'],
            'locations' => ['nullable', 'string', 'max:2000'],
        ]);

        $lead = DistributorLead::create($validated);

        SendMetaConversionEvent::dispatch('Lead', [
            'content_name' => 'Distributor Application',
            'content_category' => $validated['state'],
        ], [
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ], $request->fullUrl());

        return redirect()
            ->route('distributor.create')
            ->with('success', "Application received! Your reference is {$lead->reference}. Our team will contact you soon.");
    }

    public function status(Request $request): Response
    {
        $reference = $request->query('ref');

        $lead = is_string($reference) && $reference !== ''
            ? DistributorLead::query()->where('reference', $reference)->first()
            : null;

        return Inertia::render('distributor-status', [
            'lead' => $lead,
        ]);
    }
}
