@extends('layouts.dashboard')
@section('title', 'Audit Log | Kosanku')
@section('heading', 'Audit Log Aktivitas')
@section('subheading', 'Rekaman permanen tindakan administratif dan keamanan platform (hanya baca)')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('content')
<div class="rounded-2xl border border-[#E4E4E7] bg-white overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-[#F5F6F8] text-[11px] font-semibold text-[#71717A] uppercase tracking-wide">
                <tr>
                    <th class="p-4">Waktu</th>
                    <th>Aktor</th>
                    <th>Tindakan (Action)</th>
                    <th>Objek Terkait</th>
                    <th>Metadata</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[#E4E4E7]">
            @forelse($logs as $log)
                <tr class="hover:bg-[#F5F6F8]/60 transition">
                    <td class="p-4 text-[11px] font-mono text-[#71717A] whitespace-nowrap">{{ $log->created_at?->format('d M Y, H:i:s') }}</td>
                    <td class="font-bold text-[#18181B]">
                        {{ $log->actor?->name ?? 'Sistem / Otomatis' }}
                        @if($log->actor)
                            <span class="block text-[10px] text-[#71717A] font-normal">{{ $log->actor->role?->label() }}</span>
                        @endif
                    </td>
                    <td>
                        <span class="font-mono text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-[#EFF2FE] text-[#4263EB]">
                            {{ $log->action }}
                        </span>
                    </td>
                    <td class="text-[11px] text-[#71717A] font-mono">
                        @if($log->subject_type)
                            {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-[11px] font-mono text-[#18181B] max-w-xs truncate" title="{{ json_encode($log->metadata) }}">
                        @if($log->metadata)
                            <pre class="text-[10px] bg-[#F5F6F8] p-1.5 rounded-lg border border-[#E4E4E7] overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-[11px] text-[#71717A] font-mono">{{ $log->ip_address ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-xs text-[#71717A]">Belum ada riwayat audit log.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $logs->links() }}
</div>
@endsection
