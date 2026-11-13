<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(): View
    {
        $reviews = Review::query()->with(['user', 'property'])->latest()->paginate(20);

        return view('admin.reviews.index', compact('reviews'));
    }

    public function toggle(Request $request, Review $review, AuditLogger $logger): RedirectResponse
    {
        $review->update(['is_hidden' => ! $review->is_hidden]);
        $logger->record($request->user(), $review->is_hidden ? 'review.hidden' : 'review.shown', $review);

        return back()->with('status', 'Status ulasan diperbarui.');
    }
}
