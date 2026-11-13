<?php

namespace App\Http\Controllers;

use App\Enums\PropertyStatus;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function index(Request $request): View
    {
        $favorites = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->with(['property.images', 'property.category', 'property.facilities'])
            ->latest()
            ->paginate(12);

        if ($request->user()->isTenant() && $request->is('dashboard/*')) {
            return view('tenant.favorites.index', compact('favorites'));
        }

        return view('public.favorites', compact('favorites'));
    }

    public function toggle(Request $request, Property $property)
    {
        abort_unless($property->status === PropertyStatus::Approved, 403);

        $existing = Favorite::query()
            ->where('user_id', $request->user()->id)
            ->where('property_id', $property->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
            $message = 'Kost dihapus dari favorit.';
        } else {
            Favorite::query()->create([
                'user_id' => $request->user()->id,
                'property_id' => $property->id,
            ]);
            $favorited = true;
            $message = 'Kost disimpan ke favorit.';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'favorited' => $favorited,
                'message' => $message,
            ]);
        }

        return back()->with('status', $message);
    }
}
