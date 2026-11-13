<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $reviews = Review::query()
            ->where('user_id', $request->user()->id)
            ->with('property')
            ->latest()
            ->paginate(12);

        return view('tenant.reviews.index', compact('reviews'));
    }

    public function store(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->tenant_id === $request->user()->id, 403);
        abort_unless($booking->canBeReviewed(), 403);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        Review::query()->create([
            'booking_id' => $booking->id,
            'property_id' => $booking->property_id,
            'user_id' => $request->user()->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return back()->with('status', 'Ulasan Anda berhasil dikirim dan ditayangkan!');
    }

    public function storeForProperty(Request $request, Property $property): RedirectResponse
    {
        $tenant = $request->user();
        abort_unless($tenant->isTenant(), 403);

        $booking = Booking::query()
            ->where('tenant_id', $tenant->id)
            ->where('property_id', $property->id)
            ->whereIn('status', [BookingStatus::Approved, BookingStatus::Completed])
            ->whereDoesntHave('review')
            ->latest()
            ->first();

        if (! $booking) {
            return back()->withErrors(['review' => 'Hanya penyewa terverifikasi yang telah disetujui atau menyelesaikan sewa yang dapat memberikan ulasan.']);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        Review::query()->create([
            'booking_id' => $booking->id,
            'property_id' => $property->id,
            'user_id' => $tenant->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
        ]);

        return back()->with('status', 'Ulasan Anda berhasil dikirim dan ditayangkan!');
    }
}
