<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Report;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Booking::query()->with(['property', 'tenant', 'owner']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        return view('admin.bookings.index', [
            'bookings' => $query->latest()->paginate(20)->withQueryString(),
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['property.images', 'tenant', 'owner', 'histories.actor']);
        $reports = Report::query()->where('booking_id', $booking->id)->latest()->get();

        return view('admin.bookings.show', compact('booking', 'reports'));
    }

    public function note(Request $request, Booking $booking, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['required', 'string', 'max:1000']]);
        $logger->record($request->user(), 'booking.noted', $booking, ['notes' => $data['admin_notes']]);

        return back()->with('status', 'Catatan sengketa disimpan ke audit log.');
    }
}
