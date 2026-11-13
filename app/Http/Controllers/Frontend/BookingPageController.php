<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingPageController extends Controller
{
    public function create(Request $request, Property $property): View
    {
        $this->authorize('view', $property);

        return view('public.kost.booking', [
            'property' => $property->load(['images', 'owner']),
            'startDate' => $request->query('start_date', now()->addDay()->toDateString()),
            'duration' => $request->integer('duration_months', 1),
            'rooms' => $request->integer('rooms_requested', 1),
        ]);
    }
}
