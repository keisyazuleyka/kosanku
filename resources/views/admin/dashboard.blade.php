@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="space-y-6">
    <!-- Top Greeting & Date Header -->
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-2xl font-bold tracking-tight text-[#18181B]">Dashboard</h1>
                <span class="sr-only">Overview admin</span>
            </div>
            <p class="text-xs text-[#71717A]">Selamat datang di panel admin Kosanku</p>
        </div>
        <div class="inline-flex items-center gap-2 rounded-xl border border-[#E4E4E7] bg-white px-3.5 py-1.5 text-xs text-[#71717A] shadow-sm">
            <i data-lucide="calendar" class="h-3.5 w-3.5 text-[#4263EB]"></i>
            <span>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</span>
        </div>
    </div>

    <!-- 4 Statistic Cards -->
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Stat 1: Total Pengguna -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#71717A]">Total Pengguna</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#EFF2FE] text-[#4263EB]">
                    <i data-lucide="users" class="h-4 w-4"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#18181B]">{{ number_format($stats['users'], 0, ',', '.') }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                <i data-lucide="trending-up" class="h-3.5 w-3.5"></i>
                <span>+12% dari bulan lalu</span>
            </div>
        </div>

        <!-- Stat 2: Total Kost -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#71717A]">Total Kost</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i data-lucide="home" class="h-4 w-4"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#18181B]">{{ number_format($stats['listings'], 0, ',', '.') }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                <i data-lucide="trending-up" class="h-3.5 w-3.5"></i>
                <span>+8% dari bulan lalu</span>
            </div>
        </div>

        <!-- Stat 3: Total Booking -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#71717A]">Total Booking</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                    <i data-lucide="calendar-check" class="h-4 w-4"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#18181B]">{{ number_format($stats['bookings'], 0, ',', '.') }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium">
                <i data-lucide="trending-up" class="h-3.5 w-3.5"></i>
                <span>+15% dari bulan lalu</span>
            </div>
        </div>

        <!-- Stat 4: Pending Verifikasi -->
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#71717A]">Pending Verifikasi</span>
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                    <i data-lucide="clock" class="h-4 w-4"></i>
                </div>
            </div>
            <p class="mt-2 text-2xl font-bold tracking-tight text-[#18181B]">{{ number_format($stats['pending'], 0, ',', '.') }}</p>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-amber-700 font-medium">
                <i data-lucide="alert-circle" class="h-3.5 w-3.5"></i>
                <span>Perlu tindakan segera</span>
            </div>
        </div>
    </div>

    <!-- Charts Row: Monthly Booking Trend & Property Type Distribution -->
    <div class="grid gap-6 lg:grid-cols-3">
        <!-- Monthly Booking Line Chart -->
        <div class="card p-6 lg:col-span-2">
            <div class="flex items-center justify-between pb-4 border-b border-[#E4E4E7]">
                <div>
                    <h2 class="text-sm font-bold text-[#18181B]">Statistik Booking</h2>
                    <p class="text-xs text-[#71717A]">12 bulan terakhir</p>
                </div>
                <select class="rounded-xl border border-[#E4E4E7] bg-white px-3 py-1.5 text-xs text-[#18181B] outline-none">
                    <option value="all">Semua</option>
                    <option value="year">Tahun Ini</option>
                </select>
            </div>

            <!-- SVG Smooth Area / Line Chart -->
            <div class="mt-6">
                @php
                    $chartPoints = $monthlyBookings ?? collect();
                    $maxVal = max(5, $chartPoints->max('total') ?? 1);
                    $chartWidth = 600;
                    $chartHeight = 160;
                    $stepX = $chartPoints->count() > 1 ? $chartWidth / ($chartPoints->count() - 1) : $chartWidth;

                    $coords = [];
                    foreach ($chartPoints as $idx => $pt) {
                        $x = $idx * $stepX;
                        // Map total to Y (inverted: 0 is top, chartHeight is bottom)
                        $y = $chartHeight - (($pt['total'] / $maxVal) * ($chartHeight - 30)) - 15;
                        $coords[] = ['x' => $x, 'y' => $y, 'pt' => $pt];
                    }

                    // Build smooth bezier path
                    $pathD = '';
                    if (count($coords) > 0) {
                        $pathD = "M {$coords[0]['x']} {$coords[0]['y']}";
                        for ($i = 0; $i < count($coords) - 1; $i++) {
                            $x_mid = ($coords[$i]['x'] + $coords[$i + 1]['x']) / 2;
                            $y_mid = ($coords[$i]['y'] + $coords[$i + 1]['y']) / 2;
                            $cp_x1 = ($x_mid + $coords[$i]['x']) / 2;
                            $cp_x2 = ($x_mid + $coords[$i + 1]['x']) / 2;
                            $pathD .= " Q {$coords[$i]['x']} {$coords[$i]['y']}, {$x_mid} {$y_mid} T {$coords[$i + 1]['x']} {$coords[$i + 1]['y']}";
                        }
                    }

                    $areaD = $pathD ? $pathD . " L {$chartWidth} {$chartHeight} L 0 {$chartHeight} Z" : '';
                @endphp

                <div class="relative w-full overflow-hidden">
                    <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-44 overflow-visible" preserveAspectRatio="none">
                        <defs>
                            <linearGradient id="blueGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#4263EB" stop-opacity="0.25"/>
                                <stop offset="100%" stop-color="#4263EB" stop-opacity="0.0"/>
                            </linearGradient>
                        </defs>

                        <!-- Horizontal Grid Lines -->
                        <line x1="0" y1="20" x2="{{ $chartWidth }}" y2="20" stroke="#F4F4F5" stroke-dasharray="4"/>
                        <line x1="0" y1="70" x2="{{ $chartWidth }}" y2="70" stroke="#F4F4F5" stroke-dasharray="4"/>
                        <line x1="0" y1="120" x2="{{ $chartWidth }}" y2="120" stroke="#F4F4F5" stroke-dasharray="4"/>

                        <!-- Area Fill -->
                        @if($areaD)
                            <path d="{{ $areaD }}" fill="url(#blueGradient)" />
                            <!-- Curve Line -->
                            <path d="{{ $pathD }}" fill="none" stroke="#4263EB" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
                        @endif

                        <!-- Point Dots -->
                        @foreach($coords as $c)
                            <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="3.5" fill="#FFFFFF" stroke="#4263EB" stroke-width="2" class="transition hover:r-5 hover:stroke-width-3" />
                        @endforeach
                    </svg>

                    <!-- X-Axis Labels -->
                    <div class="mt-3 flex justify-between text-[11px] text-[#A1A1AA]">
                        @foreach($chartPoints as $pt)
                            <span>{{ $pt['label'] }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Type Distribution (Donut Chart) -->
        <div class="card p-6 flex flex-col justify-between">
            <div>
                <div class="pb-4 border-b border-[#E4E4E7]">
                    <h2 class="text-sm font-bold text-[#18181B]">Tipe Kost</h2>
                    <p class="text-xs text-[#71717A]">Distribusi listing terdaftar</p>
                </div>

                <!-- Donut Chart Graphic -->
                <div class="mt-6 flex items-center justify-center">
                    <div class="relative h-40 w-40 flex items-center justify-center">
                        <svg class="h-full w-full -rotate-90" viewBox="0 0 100 100">
                            <!-- Background Circle -->
                            <circle cx="50" cy="50" r="38" fill="none" stroke="#F4F4F5" stroke-width="12" />

                            @php
                                $circumference = 2 * pi() * 38; // approx 238.76
                                $offset = 0;
                            @endphp

                            @foreach($typesDistribution as $item)
                                @php
                                    $dash = ($item['percentage'] / 100) * $circumference;
                                @endphp
                                <circle cx="50" cy="50" r="38" fill="none"
                                        stroke="{{ $item['color'] }}"
                                        stroke-width="12"
                                        stroke-dasharray="{{ $dash }} {{ $circumference - $dash }}"
                                        stroke-dashoffset="{{ -$offset }}"
                                        stroke-linecap="round" />
                                @php
                                    $offset += $dash;
                                @endphp
                            @endforeach
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-xl font-bold tracking-tight text-[#18181B]">{{ $stats['listings'] }}</span>
                            <span class="block text-[10px] text-[#71717A]">Total Listing</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend List -->
            <div class="mt-6 space-y-2 border-t border-[#E4E4E7] pt-4">
                @foreach($typesDistribution as $item)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" style="background-color: {{ $item['color'] }}"></span>
                            <span class="text-[#71717A]">{{ $item['label'] }}</span>
                        </div>
                        <span class="font-semibold text-[#18181B]">{{ $item['percentage'] }}% ({{ $item['count'] }})</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Activity & Listings Awaiting Verification -->
    <div class="grid gap-6 lg:grid-cols-2">
        <!-- Recent Activity List -->
        <div class="card p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#E4E4E7]">
                <div>
                    <h2 class="text-sm font-bold text-[#18181B]">Aktivitas Terbaru</h2>
                    <p class="text-xs text-[#71717A]">Log tindakan pengguna dan admin</p>
                </div>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-xs font-semibold text-[#4263EB] hover:underline flex items-center gap-1">
                    Lihat semua <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>

            <div class="mt-4 divide-y divide-[#E4E4E7]">
                @forelse($recentLogs as $log)
                    <div class="flex items-start gap-3 py-3">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-[#F5F6F8] text-[#4263EB]">
                            <i data-lucide="{{ match(true) {
                                str_contains($log->action, 'property') => 'home',
                                str_contains($log->action, 'booking') => 'calendar',
                                str_contains($log->action, 'user') => 'user',
                                default => 'activity'
                            } }}" class="h-4 w-4"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold text-[#18181B]">
                                {{ $log->actor?->name ?? 'Sistem' }}
                                <span class="font-normal text-[#71717A]">melakukan</span>
                                <span class="font-mono text-[11px] text-[#4263EB] bg-[#EFF2FE] px-1.5 py-0.5 rounded">{{ $log->action }}</span>
                            </p>
                            <p class="text-[11px] text-[#A1A1AA] mt-0.5">{{ $log->created_at?->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-[#71717A]">
                        Belum ada aktivitas tercatat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Listings Awaiting Verification -->
        <div class="card p-6">
            <div class="flex items-center justify-between pb-4 border-b border-[#E4E4E7]">
                <div>
                    <h2 class="text-sm font-bold text-[#18181B]">Listing Menunggu Verifikasi</h2>
                    <p class="text-xs text-[#71717A]">Periksa data sebelum tayang ke publik</p>
                </div>
                <a href="{{ route('admin.verifikasi.index') }}" class="text-xs font-semibold text-[#4263EB] hover:underline flex items-center gap-1">
                    Lihat semua <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>

            <div class="mt-4 divide-y divide-[#E4E4E7]">
                @forelse($pendingListings as $listing)
                    <div class="flex items-center justify-between gap-3 py-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ $listing->coverUrl() }}"
                                 alt="{{ $listing->name }}"
                                 class="h-11 w-11 shrink-0 rounded-xl object-cover border border-[#E4E4E7]"
                                 onerror="this.src='{{ asset('images/kost-fallback.svg') }}'">
                            <div class="min-w-0">
                                <a href="{{ route('admin.verifikasi.show', $listing) }}" class="truncate text-xs font-semibold text-[#18181B] hover:text-[#4263EB] block">
                                    {{ $listing->name }}
                                </a>
                                <p class="text-[11px] text-[#71717A]">{{ $listing->city }} · {{ rupiah($listing->monthly_price) }}</p>
                            </div>
                        </div>
                        <div class="shrink-0">
                            <a href="{{ route('admin.verifikasi.show', $listing) }}" class="btn-ghost text-xs py-1 px-3">
                                Tinjau
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-xs text-[#71717A]">
                        <i data-lucide="check-circle" class="h-6 w-6 text-emerald-500 mx-auto mb-1"></i>
                        Semua listing telah diverifikasi.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
