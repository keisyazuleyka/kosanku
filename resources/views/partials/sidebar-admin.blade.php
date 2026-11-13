@php
    $navItem = function ($routePattern, $routeName, $label, $icon) {
        $isActive = request()->routeIs($routePattern);
        $classes = $isActive
            ? 'flex items-center gap-3 rounded-xl bg-[#EEF2FF] px-3.5 py-2.5 text-xs font-semibold text-[#405BE8] transition'
            : 'flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-medium text-[#667085] hover:bg-[#F8FAFC] hover:text-[#182230] transition';
        $iconClass = $isActive ? 'h-4 w-4 text-[#405BE8]' : 'h-4 w-4 text-[#667085]';

        return '<a href="' . route($routeName) . '" class="' . $classes . '">' .
               '<i data-lucide="' . $icon . '" class="' . $iconClass . '"></i>' .
               '<span>' . $label . '</span>' .
               '</a>';
    };
@endphp

{!! $navItem('admin.dashboard', 'admin.dashboard', 'Dashboard', 'layout-dashboard') !!}
{!! $navItem('admin.kost.*', 'admin.kost.index', 'Kost', 'home') !!}
{!! $navItem('admin.verifikasi.*', 'admin.verifikasi.index', 'Verifikasi', 'shield-check') !!}
{!! $navItem('admin.users.*', 'admin.users.index', 'Users', 'users') !!}
{!! $navItem('admin.bookings.*', 'admin.bookings.index', 'Bookings', 'calendar-days') !!}
{!! $navItem('admin.reviews.*', 'admin.reviews.index', 'Reviews', 'message-square') !!}
{!! $navItem('admin.reports.*', 'admin.reports.index', 'Reports', 'flag') !!}
{!! $navItem('admin.categories.*', 'admin.categories.index', 'Categories', 'folder-tree') !!}
{!! $navItem('admin.facilities.*', 'admin.facilities.index', 'Facilities', 'sparkles') !!}
{!! $navItem('admin.audit-logs.*', 'admin.audit-logs.index', 'Audit Logs', 'history') !!}
{!! $navItem('admin.settings.*', 'admin.settings.edit', 'Settings', 'settings') !!}
