<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->is_published, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $product->reviews()->create([
            ...$validated,
            'user_id' => $request->user()?->id,
            'status' => Review::STATUS_PENDING,
        ]);

        return back()->with('success', 'Thank you! Your review will appear once it has been approved.');
    }
}
