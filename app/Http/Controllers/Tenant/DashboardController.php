<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $userId = $request->user()->id;

        return view('tenant.dashboard', [
            'bookings' => Booking::query()->where('tenant_id', $userId)->count(),
            'favorites' => Favorite::query()->where('user_id', $userId)->count(),
            'recent' => Booking::query()->where('tenant_id', $userId)->with('property')->latest()->take(5)->get(),
        ]);
    }
}
