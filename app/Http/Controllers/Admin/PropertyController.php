<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PropertyStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Property;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Property::query()->with(['owner', 'category']);

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('city')) {
            $query->where('city', $request->string('city'));
        }

        return view('admin.properties.index', [
            'properties' => $query->latest()->paginate(15)->withQueryString(),
            'statuses' => PropertyStatus::cases(),
        ]);
    }

    public function show(Property $property): View
    {
        $property->load(['owner', 'images', 'facilities', 'category', 'reviews.user']);

        return view('admin.properties.show', compact('property'));
    }

    public function edit(Property $property): View
    {
        $property->load(['facilities', 'images']);

        return view('admin.properties.edit', [
            'property' => $property,
            'categories' => Category::query()->orderBy('name')->get(),
            'facilities' => Facility::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'city' => ['required', 'string', 'max:80'],
            'district' => ['nullable', 'string', 'max:80'],
            'address' => ['required', 'string'],
            'monthly_price' => ['required', 'integer', 'min:0'],
            'deposit' => ['required', 'integer', 'min:0'],
            'rooms_total' => ['required', 'integer', 'min:1'],
            'rooms_available' => ['required', 'integer', 'min:0'],
            'additional_fees' => ['nullable', 'string'],
            'rules' => ['nullable', 'string'],
            'is_featured' => ['sometimes', 'boolean'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
        ]);

        $property->update([
            ...$data,
            'is_featured' => $request->boolean('is_featured'),
        ]);
        $property->facilities()->sync($request->input('facilities', []));

        $logger->record($request->user(), 'property.updated', $property, ['name' => $property->name]);

        return redirect()->route('admin.kost.show', $property)->with('status', 'Listing diperbarui.');
    }

    public function destroy(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $request->validate(['confirm' => ['required', 'in:HAPUS']]);

        $logger->record($request->user(), 'property.deleted', $property, ['name' => $property->name]);
        $property->delete();

        return redirect()->route('admin.kost.index')->with('status', 'Listing dihapus.');
    }

    public function suspend(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $property->update(['status' => PropertyStatus::Suspended]);
        $logger->record($request->user(), 'property.suspended', $property);

        return back()->with('status', 'Listing ditangguhkan.');
    }

    public function activate(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $property->update(['status' => PropertyStatus::Approved, 'verified_at' => now(), 'verified_by' => $request->user()->id]);
        $logger->record($request->user(), 'property.activated', $property);

        return back()->with('status', 'Listing diaktifkan kembali.');
    }

    public function archive(Request $request, Property $property, AuditLogger $logger): RedirectResponse
    {
        $property->update(['status' => PropertyStatus::Archived]);
        $logger->record($request->user(), 'property.archived', $property);

        return back()->with('status', 'Listing berhasil diarsipkan.');
    }
}
