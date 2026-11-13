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

{!! $navItem('tenant.dashboard', 'tenant.dashboard', 'Overview', 'layout-dashboard') !!}
{!! $navItem('tenant.bookings.*', 'tenant.bookings.index', 'Booking Saya', 'calendar-days') !!}
{!! $navItem('chat.*', 'chat.index', 'Pesan & Chat', 'message-square') !!}
{!! $navItem('tenant.favorites.*', 'tenant.favorites.index', 'Favorit', 'heart') !!}
{!! $navItem('tenant.reviews.*', 'tenant.reviews.index', 'Ulasan Saya', 'star') !!}
{!! $navItem('tenant.profile', 'tenant.profile', 'Profil Saya', 'user') !!}
