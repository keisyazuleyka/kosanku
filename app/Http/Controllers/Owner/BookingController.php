<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->where('owner_id', $request->user()->id)
            ->with(['property', 'tenant'])
            ->latest()
            ->paginate(15);

        return view('owner.bookings.index', compact('bookings'));
    }

    public function show(Request $request, Booking $booking): View
    {
        $this->authorize('manageAsOwner', $booking);
        abort_unless($booking->owner_id === $request->user()->id, 403);
        $booking->load(['property.images', 'tenant', 'histories.actor']);

        return view('owner.bookings.show', compact('booking'));
    }

    public function approve(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->owner_id === $request->user()->id, 403);
        $service->changeStatus($booking, BookingStatus::Approved, $request->user(), $request->input('notes'));

        return back()->with('status', 'Booking disetujui. Ini masih pengajuan, bukan pembayaran.');
    }

    public function reject(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->owner_id === $request->user()->id, 403);
        $request->validate(['notes' => ['required', 'string', 'min:5']]);
        $service->changeStatus($booking, BookingStatus::Rejected, $request->user(), $request->input('notes'));

        return back()->with('status', 'Booking ditolak.');
    }

    public function complete(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->owner_id === $request->user()->id, 403);
        $service->changeStatus($booking, BookingStatus::Completed, $request->user(), 'Ditandai selesai oleh pemilik.');

        return back()->with('status', 'Booking ditandai selesai.');
    }
}
