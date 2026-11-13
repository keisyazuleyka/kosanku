<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'code',
    'tenant_id',
    'owner_id',
    'property_id',
    'start_date',
    'duration_months',
    'end_date',
    'monthly_price_snapshot',
    'deposit_snapshot',
    'total_estimate',
    'rooms_requested',
    'status',
    'tenant_notes',
    'owner_notes',
    'cancelled_at',
    'cancel_reason',
    'inventory_held',
])]
class Booking extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => BookingStatus::class,
            'duration_months' => 'integer',
            'monthly_price_snapshot' => 'integer',
            'deposit_snapshot' => 'integer',
            'total_estimate' => 'integer',
            'rooms_requested' => 'integer',
            'cancelled_at' => 'datetime',
            'inventory_held' => 'boolean',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(BookingStatusHistory::class)->latest();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function canBeCancelledByTenant(): bool
    {
        return $this->status === BookingStatus::Pending;
    }

    public function canBeReviewed(): bool
    {
        return in_array($this->status, [BookingStatus::Approved, BookingStatus::Completed], true)
            && $this->review()->doesntExist();
    }
}
