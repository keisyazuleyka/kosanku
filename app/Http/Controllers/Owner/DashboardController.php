<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $ownerId = $request->user()->id;

        $stats = [
            'listings' => Property::query()->where('owner_id', $ownerId)->count(),
            'pending' => Property::query()->where('owner_id', $ownerId)->where('status', 'pending')->count(),
            'approved' => Property::query()->where('owner_id', $ownerId)->where('status', 'approved')->count(),
            'bookings' => Booking::query()->where('owner_id', $ownerId)->count(),
            'booking_pending' => Booking::query()->where('owner_id', $ownerId)->where('status', 'pending')->count(),
        ];

        $recentBookings = Booking::query()
            ->where('owner_id', $ownerId)
            ->with(['property', 'tenant'])
            ->latest()
            ->take(6)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentBookings'));
    }
}
