<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\BookingStatus;
use App\Enums\PropertyType;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Favorite;
use App\Models\Property;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyListingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::query()
            ->approved()
            ->with(['images', 'category', 'facilities'])
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_hidden', false)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_hidden', false)]);

        $selectedCampus = null;
        $selectedRadius = (float) ($request->input('radius') ?: 5);
        $userLat = $request->filled('lat') ? (float) $request->input('lat') : null;
        $userLng = $request->filled('lng') ? (float) $request->input('lng') : null;

        // METHOD 1: Pencarian Berdasarkan Kampus (Kota Dulu -> Kampus -> Radius)
        if ($request->filled('campus_id')) {
            $selectedCampus = Campus::query()->active()->find($request->input('campus_id'));
            if ($selectedCampus) {
                $query->withinRadius($selectedCampus->latitude, $selectedCampus->longitude, $selectedRadius);
            }
        }
        // METHOD 3: Pencarian Berdasarkan Lokasi Saya (GPS Geolocation + Radius)
        elseif ($userLat !== null && $userLng !== null) {
            $query->withinRadius($userLat, $userLng, $selectedRadius);
        }
        // METHOD 2: Pencarian Berdasarkan Kota / Daerah / Kata Kunci
        else {
            if ($search = $request->string('q')->toString()) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhere('district', 'like', "%{$search}%")
                        ->orWhere('village', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $location = trim($request->string('location')->toString() ?: $request->string('city')->toString());
            if ($location !== '') {
                $query->where(function ($q) use ($location) {
                    $q->where('city', 'like', "%{$location}%")
                        ->orWhere('district', 'like', "%{$location}%")
                        ->orWhere('village', 'like', "%{$location}%")
                        ->orWhere('address', 'like', "%{$location}%");
                });
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->integer('category'));
        }

        // Filter Harga (Validasi Min & Max)
        $minPrice = $request->filled('min_price') ? $request->integer('min_price') : null;
        $maxPrice = $request->filled('max_price') ? $request->integer('max_price') : null;
        if ($minPrice !== null && $maxPrice !== null && $minPrice > $maxPrice) {
            [$minPrice, $maxPrice] = [$maxPrice, $minPrice];
        }
        if ($minPrice !== null && $minPrice > 0) {
            $query->where('monthly_price', '>=', $minPrice);
        }
        if ($maxPrice !== null && $maxPrice > 0) {
            $query->where('monthly_price', '<=', $maxPrice);
        }

        if ($request->filled('facilities')) {
            $ids = array_filter((array) $request->input('facilities'));
            foreach ($ids as $id) {
                $query->whereHas('facilities', fn ($q) => $q->where('facilities.id', $id));
            }
        }

        $sort = $request->string('sort')->toString();
        if ($sort) {
            match ($sort) {
                'price_asc' => $query->orderBy('monthly_price'),
                'price_desc' => $query->orderByDesc('monthly_price'),
                'rating' => $query->orderByDesc('reviews_avg_rating'),
                'distance' => ($selectedCampus || ($userLat && $userLng)) ? null : $query->latest(),
                default => $query->latest(),
            };
        } elseif (! $selectedCampus && ! ($userLat && $userLng)) {
            $query->latest();
        }

        $properties = $query->paginate(9)->withQueryString();

        $favoriteIds = auth()->check()
            ? Favorite::query()->where('user_id', auth()->id())->pluck('property_id')
            : collect();

        $selectedCity = $request->string('city')->toString() ?: ($selectedCampus?->city ?? '');
        $campusesInCity = $selectedCity
            ? Campus::query()->active()->where('city', $selectedCity)->orderBy('name')->get()
            : collect();

        $allCities = Campus::query()->active()->distinct()->pluck('city')
            ->concat(Property::query()->approved()->distinct()->pluck('city'))
            ->unique()
            ->sort()
            ->values();

        $mapProperties = ($userLat !== null && $userLng !== null)
            ? $properties->getCollection()->map(function ($p) use ($userLat, $userLng) {
                $dist = $p->distance_km ?? $p->distanceTo($userLat, $userLng);

                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'type' => $p->type->label(),
                    'price' => rupiah($p->monthly_price),
                    'monthly_price' => $p->monthly_price,
                    'address' => ($p->district ? $p->district.', ' : '').$p->city,
                    'lat' => (float) $p->latitude,
                    'lng' => (float) $p->longitude,
                    'distance_km' => $dist !== null ? round((float) $dist, 1) : null,
                    'distance_text' => $dist !== null ? number_format((float) $dist, 1).' km' : null,
                    'rating' => number_format($p->averageRating() ?: 4.8, 1),
                    'cover_url' => $p->coverUrl(),
                    'detail_url' => route('kost.show', $p->slug),
                ];
            })->values()
            : collect();

        return view('public.kost.index', [
            'properties' => $properties,
            'mapProperties' => $mapProperties,
            'cities' => $allCities,
            'selectedCity' => $selectedCity,
            'selectedCampus' => $selectedCampus,
            'selectedRadius' => $selectedRadius,
            'campusesInCity' => $campusesInCity,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'categories' => Category::query()->where('is_active', true)->get(),
            'facilities' => Facility::query()->where('is_active', true)->get(),
            'types' => PropertyType::cases(),
            'favoriteIds' => $favoriteIds,
        ]);
    }

    public function nearby(Request $request): JsonResponse
    {
        $lat = (float) $request->input('lat');
        $lng = (float) $request->input('lng');
        $radius = (float) ($request->input('radius') ?: 5);

        if (! $lat || ! $lng) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat latitude dan longitude diperlukan.',
                'properties' => [],
            ], 422);
        }

        $query = Property::query()
            ->approved()
            ->withinRadius($lat, $lng, $radius)
            ->with(['images', 'category']);

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('min_price')) {
            $query->where('monthly_price', '>=', $request->integer('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('monthly_price', '<=', $request->integer('max_price'));
        }

        $properties = $query->get()->map(function ($p) use ($lat, $lng) {
            $dist = $p->distance_km ?? $p->distanceTo($lat, $lng);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'type' => $p->type->label(),
                'price' => rupiah($p->monthly_price),
                'monthly_price' => $p->monthly_price,
                'address' => ($p->district ? $p->district.', ' : '').$p->city,
                'full_address' => $p->address.', '.$p->city,
                'lat' => (float) $p->latitude,
                'lng' => (float) $p->longitude,
                'distance_km' => $dist !== null ? round((float) $dist, 1) : null,
                'distance_text' => $dist !== null ? number_format((float) $dist, 1).' km' : null,
                'rating' => number_format($p->averageRating() ?: 4.8, 1),
                'reviews_count' => $p->reviews()->count(),
                'rooms_available' => $p->rooms_available,
                'cover_url' => $p->coverUrl(),
                'detail_url' => route('kost.show', $p->slug),
            ];
        })->sortBy('distance_km')->values();

        return response()->json([
            'success' => true,
            'count' => $properties->count(),
            'radius_km' => $radius,
            'user_location' => [
                'lat' => $lat,
                'lng' => $lng,
            ],
            'properties' => $properties,
        ]);
    }

    public function show(string $slug): View
    {
        $property = Property::query()
            ->where('slug', $slug)
            ->with(['images', 'facilities', 'category', 'owner', 'reviews' => fn ($q) => $q->visible()->latest()->with('user')])
            ->firstOrFail();

        $this->authorize('view', $property);

        $isFavorited = auth()->check()
            && Favorite::query()->where('user_id', auth()->id())->where('property_id', $property->id)->exists();

        $userEligibleBooking = null;
        $userExistingReview = null;

        if (auth()->check() && auth()->user()->isTenant()) {
            $userEligibleBooking = Booking::query()
                ->where('tenant_id', auth()->id())
                ->where('property_id', $property->id)
                ->whereIn('status', [BookingStatus::Approved, BookingStatus::Completed])
                ->whereDoesntHave('review')
                ->latest()
                ->first();

            $userExistingReview = Review::query()
                ->where('user_id', auth()->id())
                ->where('property_id', $property->id)
                ->first();
        }

        $nearbyCampuses = collect();
        if ($property->latitude && $property->longitude) {
            $nearbyCampuses = Campus::query()
                ->active()
                ->get()
                ->map(function ($c) use ($property) {
                    $c->distance_km = $property->distanceTo($c->latitude, $c->longitude);

                    return $c;
                })
                ->filter(fn ($c) => $c->distance_km !== null && $c->distance_km <= 8.0)
                ->sortBy('distance_km')
                ->values()
                ->take(4);
        }

        $nearbyAmenities = [
            [
                'name' => 'Minimarket (Indomaret / Alfamart)',
                'distance' => '200 - 350 m',
                'icon' => 'shopping-bag',
                'type' => 'Belanja Harian',
            ],
            [
                'name' => 'ATM & Perbankan (BCA, Mandiri, BRI)',
                'distance' => '400 m',
                'icon' => 'credit-card',
                'type' => 'Perbankan',
            ],
            [
                'name' => 'Apotek & Klinik Medis Terdekat',
                'distance' => '600 m',
                'icon' => 'shield-plus',
                'type' => 'Kesehatan',
            ],
            [
                'name' => 'Stasiun Kereta / Halte Angkutan',
                'distance' => '850 m',
                'icon' => 'bus',
                'type' => 'Transportasi',
            ],
            [
                'name' => 'Pujasera & Kafe Nyaman Belajar',
                'distance' => '250 m',
                'icon' => 'utensils',
                'type' => 'Kuliner',
            ],
        ];

        return view('public.kost.show', compact('property', 'isFavorited', 'userEligibleBooking', 'userExistingReview', 'nearbyCampuses', 'nearbyAmenities'));
    }
}
