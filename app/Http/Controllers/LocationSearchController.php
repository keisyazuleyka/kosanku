<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationSearchController extends Controller
{
    public function cities(): JsonResponse
    {
        $campusCities = Campus::query()->active()->distinct()->pluck('city');
        $propertyCities = Property::query()->approved()->distinct()->pluck('city');

        $cities = $campusCities->concat($propertyCities)->unique()->sort()->values();

        return response()->json($cities);
    }

    public function campuses(Request $request): JsonResponse
    {
        $city = trim($request->string('city')->toString());

        // ENFORCING MANDATORY WORKFLOW: City must be selected first!
        if (empty($city)) {
            return response()->json([]);
        }

        $campuses = Campus::query()
            ->active()
            ->where('city', $city)
            ->orderBy('name')
            ->get(['id', 'name', 'short_name', 'city', 'address', 'latitude', 'longitude']);

        return response()->json($campuses);
    }

    public function search(Request $request): JsonResponse
    {
        $term = trim($request->string('q')->toString());

        if (strlen($term) < 1) {
            $popularCities = Property::query()
                ->approved()
                ->select('city')
                ->distinct()
                ->orderBy('city')
                ->limit(6)
                ->pluck('city')
                ->map(fn ($city) => [
                    'label' => $city,
                    'type' => 'Kota Populer',
                ]);

            return response()->json($popularCities);
        }

        $results = collect();

        // 1. Matches from City
        $cities = Property::query()
            ->approved()
            ->where('city', 'like', "%{$term}%")
            ->select('city')
            ->distinct()
            ->limit(5)
            ->pluck('city')
            ->map(fn ($city) => [
                'label' => $city,
                'type' => 'Kota / Kabupaten',
            ]);

        $results = $results->concat($cities);

        // 2. Matches from District (Kecamatan)
        $districts = Property::query()
            ->approved()
            ->whereNotNull('district')
            ->where('district', 'like', "%{$term}%")
            ->select('district', 'city')
            ->distinct()
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->district.($row->city ? ', '.$row->city : ''),
                'type' => 'Kecamatan',
            ]);

        $results = $results->concat($districts);

        // 3. Matches from Village (Kelurahan) if any
        $villages = Property::query()
            ->approved()
            ->whereNotNull('village')
            ->where('village', 'like', "%{$term}%")
            ->select('village', 'district', 'city')
            ->distinct()
            ->limit(5)
            ->get()
            ->map(fn ($row) => [
                'label' => $row->village.($row->district ? ', '.$row->district : ''),
                'type' => 'Kelurahan / Desa',
            ]);

        $results = $results->concat($villages);

        return response()->json($results->unique('label')->values()->take(8));
    }
}
