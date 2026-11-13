<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'users' => User::query()->count(),
            'owners' => User::query()->where('role', UserRole::Owner)->count(),
            'tenants' => User::query()->where('role', UserRole::Tenant)->count(),
            'listings' => Property::query()->count(),
            'pending' => Property::query()->where('status', PropertyStatus::Pending)->count(),
            'active' => Property::query()->where('status', PropertyStatus::Approved)->count(),
            'bookings' => Booking::query()->count(),
        ];

        // 14-day booking query
        $bookingChart = Booking::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(13)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->pluck('total', 'day');

        $days = collect(range(13, 0))->map(function ($i) use ($bookingChart) {
            $day = now()->subDays($i)->toDateString();

            return ['label' => now()->subDays($i)->format('d M'), 'total' => (int) ($bookingChart[$day] ?? 0)];
        });

        // 12-Month Monthly Booking trend for line chart
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyBookings = collect(range(1, 12))->map(function ($m) use ($months) {
            $count = Booking::query()
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', $m)
                ->count();

            return [
                'month' => $m,
                'label' => $months[$m - 1],
                'total' => $count,
            ];
        });

        // Property Type Distribution for Donut Chart
        $totalProps = max(1, Property::query()->count());
        $typeCounts = Property::query()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type');

        $typesDistribution = collect(PropertyType::cases())->map(function ($type) use ($typeCounts, $totalProps) {
            $count = (int) ($typeCounts[$type->value] ?? 0);
            $percentage = round(($count / $totalProps) * 100);

            return [
                'type' => $type,
                'label' => $type->label(),
                'count' => $count,
                'percentage' => $percentage,
                'color' => match ($type->value) {
                    'putra' => '#4263EB',
                    'putri' => '#7C3AED',
                    'campur' => '#06B6D4',
                    default => '#10B981',
                },
            ];
        });

        // Pending verifications with thumbnails
        $pendingListings = Property::query()
            ->where('status', PropertyStatus::Pending)
            ->with(['owner', 'images'])
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = AuditLog::query()->with('actor')->latest('id')->take(6)->get();
        $recentBookings = Booking::query()->with(['property', 'tenant'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'days',
            'monthlyBookings',
            'typesDistribution',
            'pendingListings',
            'recentLogs',
            'recentBookings'
        ));
    }
}
