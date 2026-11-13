<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->whereHas('property', fn ($q) => $q->where('owner_id', $request->user()->id))
            ->with(['user', 'property'])
            ->latest()
            ->paginate(15);

        return view('owner.reviews.index', compact('reviews'));
    }
}
