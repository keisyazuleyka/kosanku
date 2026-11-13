<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\PropertyStatus;
use App\Models\Booking;
use App\Models\BookingStatusHistory;
use App\Models\Property;
use App\Models\User;
use App\Notifications\AppNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function create(User $tenant, Property $property, array $data): Booking
    {
        if ($property->status !== PropertyStatus::Approved) {
            throw ValidationException::withMessages([
                'property' => 'Kost ini belum dapat dipesan.',
            ]);
        }

        if ($property->rooms_available < (int) ($data['rooms_requested'] ?? 1)) {
            throw ValidationException::withMessages([
                'rooms_requested' => 'Kamar tersedia tidak mencukupi.',
            ]);
        }

        $duration = (int) $data['duration_months'];
        $start = Carbon::parse($data['start_date'])->startOfDay();
        $end = $start->copy()->addMonths($duration)->subDay();
        $rooms = (int) ($data['rooms_requested'] ?? 1);
        $total = ($property->monthly_price * $duration * $rooms) + $property->deposit;

        return DB::transaction(function () use ($tenant, $property, $data, $duration, $start, $end, $rooms, $total) {
            $booking = Booking::query()->create([
                'code' => 'KS-'.strtoupper(Str::random(8)),
                'tenant_id' => $tenant->id,
                'owner_id' => $property->owner_id,
                'property_id' => $property->id,
                'start_date' => $start,
                'duration_months' => $duration,
                'end_date' => $end,
                'monthly_price_snapshot' => $property->monthly_price,
                'deposit_snapshot' => $property->deposit,
                'total_estimate' => $total,
                'rooms_requested' => $rooms,
                'status' => BookingStatus::Pending,
                'tenant_notes' => $data['tenant_notes'] ?? null,
            ]);

            $this->history($booking, null, BookingStatus::Pending, $tenant, 'Pengajuan booking dibuat. Belum merupakan transaksi berbayar.');

            if ($property->owner) {
                $property->owner->notify(new AppNotification(
                    title: 'Pengajuan Booking Baru',
                    message: 'Penyewa '.$tenant->name.' mengajukan sewa untuk "'.$property->name.'" ('.$duration.' bulan).',
                    url: route('owner.bookings.show', $booking),
                    type: 'booking',
                    icon: 'calendar-check'
                ));
            }

            return $booking;
        });
    }

    public function changeStatus(Booking $booking, BookingStatus $status, User $actor, ?string $notes = null): Booking
    {
        return DB::transaction(function () use ($booking, $status, $actor, $notes) {
            $booking->refresh();
            $from = $booking->status;

            $this->assertTransition($booking, $status, $actor);
            $this->adjustInventory($booking, $status);

            $booking->update([
                'status' => $status,
                'owner_notes' => $actor->isOwner() || $actor->isAdmin() ? ($notes ?? $booking->owner_notes) : $booking->owner_notes,
                'cancelled_at' => $status === BookingStatus::Cancelled ? now() : $booking->cancelled_at,
                'cancel_reason' => $status === BookingStatus::Cancelled ? $notes : $booking->cancel_reason,
            ]);

            $this->history($booking, $from, $status, $actor, $notes);

            if ($actor->id !== $booking->tenant_id && $booking->tenant) {
                $booking->tenant->notify(new AppNotification(
                    title: 'Status Booking: '.$status->label(),
                    message: 'Pengajuan sewa Anda untuk "'.$booking->property->name.'" telah '.$status->label().'.',
                    url: route('tenant.bookings.show', $booking),
                    type: 'booking',
                    icon: 'calendar-days'
                ));
            } elseif ($actor->id === $booking->tenant_id && $booking->owner) {
                $booking->owner->notify(new AppNotification(
                    title: 'Pengajuan Sewa Dibatalkan',
                    message: 'Penyewa '.$booking->tenant->name.' membatalkan pengajuan sewa untuk "'.$booking->property->name.'".',
                    url: route('owner.bookings.show', $booking),
                    type: 'booking',
                    icon: 'x-circle'
                ));
            }

            return $booking->fresh();
        });
    }

    private function assertTransition(Booking $booking, BookingStatus $to, User $actor): void
    {
        $from = $booking->status;

        $allowed = match ($from) {
            BookingStatus::Pending => [BookingStatus::Approved, BookingStatus::Rejected, BookingStatus::Cancelled],
            BookingStatus::Approved => [BookingStatus::Completed, BookingStatus::Cancelled],
            default => [],
        };

        if (! in_array($to, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => 'Perubahan status booking tidak diizinkan.',
            ]);
        }

        if ($to === BookingStatus::Cancelled && $actor->isTenant() && $from !== BookingStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => 'Pengajuan yang sudah diproses tidak dapat dibatalkan oleh penyewa.',
            ]);
        }

        if ($to === BookingStatus::Approved && $booking->property->rooms_available < $booking->rooms_requested) {
            throw ValidationException::withMessages([
                'status' => 'Kamar sudah tidak tersedia.',
            ]);
        }
    }

    private function adjustInventory(Booking $booking, BookingStatus $to): void
    {
        $property = Property::query()->lockForUpdate()->findOrFail($booking->property_id);

        if ($to === BookingStatus::Approved && ! $booking->inventory_held) {
            if ($property->rooms_available < $booking->rooms_requested) {
                throw ValidationException::withMessages([
                    'status' => 'Kamar sudah tidak tersedia.',
                ]);
            }

            $property->decrement('rooms_available', $booking->rooms_requested);
            $booking->inventory_held = true;
            $booking->save();
        }

        if (
            in_array($to, [BookingStatus::Cancelled, BookingStatus::Rejected, BookingStatus::Completed], true)
            && $booking->inventory_held
            && $to !== BookingStatus::Completed
        ) {
            $property->increment('rooms_available', $booking->rooms_requested);
            $booking->inventory_held = false;
            $booking->save();
        }
    }

    private function history(Booking $booking, ?BookingStatus $from, BookingStatus $to, ?User $actor, ?string $notes): void
    {
        BookingStatusHistory::query()->create([
            'booking_id' => $booking->id,
            'from_status' => $from?->value,
            'to_status' => $to->value,
            'actor_id' => $actor?->id,
            'notes' => $notes,
        ]);
    }
}
