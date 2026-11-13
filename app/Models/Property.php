<?php

namespace App\Models;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use Database\Factories\PropertyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'owner_id',
    'category_id',
    'name',
    'slug',
    'description',
    'type',
    'city',
    'district',
    'village',
    'address',
    'latitude',
    'longitude',
    'monthly_price',
    'deposit',
    'additional_fees',
    'rules',
    'rooms_total',
    'rooms_available',
    'status',
    'is_featured',
    'rejection_reason',
    'revision_request',
    'verified_by',
    'verified_at',
    'is_demo',
])]
class Property extends Model
{
    /** @use HasFactory<PropertyFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => PropertyType::class,
            'status' => PropertyStatus::class,
            'latitude' => 'float',
            'longitude' => 'float',
            'monthly_price' => 'integer',
            'deposit' => 'integer',
            'rooms_total' => 'integer',
            'rooms_available' => 'integer',
            'is_featured' => 'boolean',
            'verified_at' => 'datetime',
            'is_demo' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function images(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->orderBy('sort_order');
    }

    public function coverImage(): HasMany
    {
        return $this->hasMany(PropertyImage::class)->where('is_cover', true);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class)->withTimestamps();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', PropertyStatus::Approved);
    }

    public function scopeWithinRadius(Builder $query, float $latitude, float $longitude, float $radiusKm): Builder
    {
        $driver = $query->getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            $latDelta = $radiusKm / 111.0;
            $lngDelta = $radiusKm / (111.0 * max(0.1, cos(deg2rad($latitude))));

            return $query
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereBetween('latitude', [$latitude - $latDelta, $latitude + $latDelta])
                ->whereBetween('longitude', [$longitude - $lngDelta, $longitude + $lngDelta]);
        }

        $haversine = '(6371 * acos(least(1.0, greatest(-1.0, cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude))))))';

        return $query
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("properties.*, {$haversine} AS distance_km", [$latitude, $longitude, $latitude])
            ->whereRaw("{$haversine} <= ?", [$latitude, $longitude, $latitude, $radiusKm])
            ->orderBy('distance_km');
    }

    public function distanceTo(?float $latitude, ?float $longitude): ?float
    {
        if ($latitude === null || $longitude === null || $this->latitude === null || $this->longitude === null) {
            return null;
        }

        $earthRadius = 6371;
        $dLat = deg2rad($latitude - $this->latitude);
        $dLon = deg2rad($longitude - $this->longitude);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($this->latitude)) * cos(deg2rad($latitude)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->status === PropertyStatus::Approved;
    }

    public function hasAvailability(): bool
    {
        return $this->rooms_available > 0;
    }

    public function coverUrl(): string
    {
        $cover = $this->images->firstWhere('is_cover', true) ?? $this->images->first();

        return $cover?->url() ?? asset('images/kost-fallback.svg');
    }

    public function averageRating(): ?float
    {
        $avg = $this->reviews()->where('is_hidden', false)->avg('rating');

        return $avg ? round((float) $avg, 1) : null;
    }
}
