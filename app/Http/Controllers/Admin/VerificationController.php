<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Property;
use App\Notifications\AppNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::query()->with('owner');

        $status = $request->string('status')->toString() ?: PropertyStatus::Pending->value;
        $query->where('status', $status);

        return view('admin.verifications.index', [
            'properties' => $query->latest()->paginate(12)->withQueryString(),
            'currentStatus' => $status,
            'statuses' => PropertyStatus::cases(),
        ]);
    }

    public function show(Property $property): View
    {
        $property->load(['owner', 'images', 'facilities', 'category']);
        $history = AuditLog::query()
            ->where('subject_type', Property::class)
            ->where('subject_id', $property->id)
            ->with('actor')
            ->latest('id')
            ->get();

        return view('admin.verifications.show', compact('property', 'history'));
    }

    public function approve(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $property->update([
            'status' => PropertyStatus::Approved,
            'rejection_reason' => null,
            'revision_request' => null,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $logger->record($request->user(), 'property.approved', $property);

        // Notify property owner
        $property->owner?->notify(new AppNotification(
            title: 'Listing Kost Disetujui!',
            message: "Listing '{$property->name}' telah diverifikasi dan disetujui oleh admin Kosanku. Sekarang listing Anda sudah tayang ke publik.",
            url: route('owner.kost.edit', $property),
            type: 'success'
        ));

        return redirect()->route('admin.verifikasi.index')->with('status', 'Listing disetujui.');
    }

    public function reject(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10'],
        ]);

        $property->update([
            'status' => PropertyStatus::Rejected,
            'rejection_reason' => $data['rejection_reason'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        $logger->record($request->user(), 'property.rejected', $property, ['reason' => $data['rejection_reason']]);

        // Notify property owner
        $property->owner?->notify(new AppNotification(
            title: 'Listing Kost Perlu Perbaikan / Ditolak',
            message: "Listing '{$property->name}' belum dapat disetujui: {$data['rejection_reason']}",
            url: route('owner.kost.edit', $property),
            type: 'danger'
        ));

        return redirect()->route('admin.verifikasi.index')->with('status', 'Listing ditolak.');
    }

    public function requestRevision(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'revision_request' => ['required', 'string', 'min:10'],
        ]);

        $property->update([
            'status' => PropertyStatus::Draft,
            'revision_request' => $data['revision_request'],
        ]);

        $logger->record($request->user(), 'property.revision_requested', $property, ['notes' => $data['revision_request']]);

        // Notify property owner
        $property->owner?->notify(new AppNotification(
            title: 'Permintaan Revisi Listing Kost',
            message: "Listing '{$property->name}' memerlukan revisi dari admin: {$data['revision_request']}",
            url: route('owner.kost.edit', $property),
            type: 'warning'
        ));

        return back()->with('status', 'Permintaan revisi dikirim ke pemilik.');
    }
}
