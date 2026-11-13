<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StoreBookingRequest;
use App\Models\Booking;
use App\Models\Property;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->where('tenant_id', $request->user()->id)
            ->with(['property.images'])
            ->latest()
            ->paginate(12);

        return view('tenant.bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        abort_unless($booking->tenant_id === $request->user()->id, 403);
        $booking->load(['property.images', 'histories.actor', 'review']);

        return view('tenant.bookings.show', compact('booking'));
    }

    public function store(StoreBookingRequest $request, Property $property, BookingService $service): RedirectResponse
    {
        $booking = $service->create($request->user(), $property, $request->validated());

        return redirect()
            ->route('tenant.bookings.show', $booking)
            ->with('status', 'Pengajuan tersimpan. Ini bukan transaksi berbayar.');
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->tenant_id === $request->user()->id, 403);
        $request->validate(['cancel_reason' => ['required', 'string', 'min:5']]);
        $service->changeStatus($booking, BookingStatus::Cancelled, $request->user(), $request->input('cancel_reason'));

        return back()->with('status', 'Pengajuan dibatalkan.');
    }
}
