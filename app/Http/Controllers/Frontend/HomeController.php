<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Campus;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Favorite;
use App\Models\Property;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        // 1. Paling Direkomendasikan (Approved, Ada Kamar, Foto Lengkap, Rating Valid / Unggulan)
        $recommended = Property::query()
            ->approved()
            ->where('rooms_available', '>', 0)
            ->with(['images', 'category', 'owner'])
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_hidden', false)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_hidden', false)])
            ->orderByDesc('is_featured')
            ->orderByDesc('reviews_avg_rating')
            ->latest()
            ->take(4)
            ->get();

        // 2. Kamu Mungkin Suka (Personalisasi jika ada riwayat favorit/kota, atau fallback approved relevan)
        $personalizedQuery = Property::query()
            ->approved()
            ->where('rooms_available', '>', 0)
            ->whereNotIn('id', $recommended->pluck('id'))
            ->with(['images', 'category', 'owner'])
            ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_hidden', false)], 'rating')
            ->withCount(['reviews as reviews_count' => fn ($q) => $q->where('is_hidden', false)]);

        if (auth()->check()) {
            $favProps = Property::query()->whereIn('id', auth()->user()->favorites()->pluck('property_id'))->get();
            $favCatIds = $favProps->pluck('category_id')->filter()->unique();
            $favCities = $favProps->pluck('city')->filter()->unique();

            if ($favCatIds->isNotEmpty() || $favCities->isNotEmpty()) {
                $personalizedQuery->where(function ($q) use ($favCatIds, $favCities) {
                    if ($favCatIds->isNotEmpty()) {
                        $q->whereIn('category_id', $favCatIds);
                    }
                    if ($favCities->isNotEmpty()) {
                        $q->orWhereIn('city', $favCities);
                    }
                });
            }
        }

        $personalized = $personalizedQuery->latest()->take(4)->get();
        if ($personalized->isEmpty()) {
            $personalized = Property::query()
                ->approved()
                ->where('rooms_available', '>', 0)
                ->whereNotIn('id', $recommended->pluck('id'))
                ->with(['images', 'category', 'owner'])
                ->withAvg(['reviews as reviews_avg_rating' => fn ($q) => $q->where('is_hidden', false)], 'rating')
                ->latest()
                ->take(4)
                ->get();
        }

        // Daftar Kota Terintegrasi (dari Kampus & Kost)
        $allCities = Campus::query()->active()->distinct()->pluck('city')
            ->concat(Property::query()->approved()->distinct()->pluck('city'))
            ->unique()
            ->sort()
            ->values();

        $popularAreas = Property::query()
            ->approved()
            ->selectRaw('city, COUNT(*) as total')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();

        $favoriteIds = auth()->check()
            ? Favorite::query()->where('user_id', auth()->id())->pluck('property_id')
            : collect();

        return view('public.home', [
            'recommended' => $recommended,
            'personalized' => $personalized,
            'featured' => $recommended, // fallback compatibility
            'cities' => $allCities,
            'categories' => Category::query()->where('is_active', true)->get(),
            'facilities' => Facility::query()->where('is_active', true)->get(),
            'popularAreas' => $popularAreas,
            'favoriteIds' => $favoriteIds,
        ]);
    }
}
