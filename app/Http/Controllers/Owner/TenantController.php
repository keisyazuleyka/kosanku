<?php

namespace App\Http\Controllers\Owner;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $tenants = Booking::query()
            ->where('owner_id', $request->user()->id)
            ->whereIn('status', [BookingStatus::Approved, BookingStatus::Completed])
            ->with(['tenant', 'property'])
            ->latest()
            ->paginate(15);

        return view('owner.tenants.index', compact('tenants'));
    }
}
