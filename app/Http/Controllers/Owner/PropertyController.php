<?php

namespace App\Http\Controllers\Owner;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePropertyRequest;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Services\PropertyImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function index(Request $request): View
    {
        $properties = Property::query()
            ->where('owner_id', $request->user()->id)
            ->with('images')
            ->latest()
            ->paginate(10);

        return view('owner.properties.index', compact('properties'));
    }

    public function create(): View
    {
        return view('owner.properties.form', [
            'property' => new Property,
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'facilities' => Facility::query()->where('is_active', true)->orderBy('name')->get(),
            'types' => PropertyType::cases(),
        ]);
    }

    public function store(StorePropertyRequest $request, PropertyImageService $images): RedirectResponse
    {
        $data = $request->validated();
        $status = $data['submit_action'] === 'pending' ? PropertyStatus::Pending : PropertyStatus::Draft;

        $property = Property::query()->create([
            'owner_id' => $request->user()->id,
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'slug' => $this->uniqueSlug($data['name']),
            'description' => $data['description'],
            'type' => $data['type'],
            'city' => $data['city'],
            'district' => $data['district'] ?? null,
            'village' => $data['village'] ?? null,
            'address' => $data['address'],
            'monthly_price' => $data['monthly_price'],
            'deposit' => $data['deposit'],
            'additional_fees' => $data['additional_fees'] ?? null,
            'rules' => $data['rules'] ?? null,
            'rooms_total' => $data['rooms_total'],
            'rooms_available' => $data['rooms_available'],
            'status' => $status,
        ]);

        $property->facilities()->sync($data['facilities'] ?? []);

        if ($request->hasFile('images')) {
            $images->store($property, $request->file('images'));
        }

        return redirect()->route('owner.kost.index')->with('status', 'Kost disimpan.');
    }

    public function edit(Request $request, Property $kost): View
    {
        abort_unless($kost->owner_id === $request->user()->id, 403);
        $kost->load(['images', 'facilities']);

        return view('owner.properties.form', [
            'property' => $kost,
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'facilities' => Facility::query()->where('is_active', true)->orderBy('name')->get(),
            'types' => PropertyType::cases(),
        ]);
    }

    public function update(StorePropertyRequest $request, Property $kost, PropertyImageService $images): RedirectResponse
    {
        abort_unless($kost->owner_id === $request->user()->id, 403);
        $data = $request->validated();

        $status = $kost->status;
        if ($data['submit_action'] === 'pending') {
            $status = PropertyStatus::Pending;
        } elseif ($kost->status === PropertyStatus::Draft) {
            $status = PropertyStatus::Draft;
        }

        $kost->update([
            'category_id' => $data['category_id'] ?? null,
            'name' => $data['name'],
            'description' => $data['description'],
            'type' => $data['type'],
            'city' => $data['city'],
            'district' => $data['district'] ?? null,
            'village' => $data['village'] ?? null,
            'address' => $data['address'],
            'monthly_price' => $data['monthly_price'],
            'deposit' => $data['deposit'],
            'additional_fees' => $data['additional_fees'] ?? null,
            'rules' => $data['rules'] ?? null,
            'rooms_total' => $data['rooms_total'],
            'rooms_available' => $data['rooms_available'],
            'status' => $status,
        ]);

        $kost->facilities()->sync($data['facilities'] ?? []);

        if ($request->hasFile('images')) {
            $images->store($kost, $request->file('images'));
        }

        return redirect()->route('owner.kost.index')->with('status', 'Kost diperbarui.');
    }

    public function destroy(Request $request, Property $kost): RedirectResponse
    {
        abort_unless($kost->owner_id === $request->user()->id, 403);
        $request->validate(['confirm' => ['required', 'in:HAPUS']]);
        $kost->delete();

        return redirect()->route('owner.kost.index')->with('status', 'Kost dihapus.');
    }

    public function destroyImage(Request $request, Property $kost, PropertyImage $image, PropertyImageService $images): RedirectResponse
    {
        abort_unless($kost->owner_id === $request->user()->id, 403);
        abort_unless($image->property_id === $kost->id, 403);
        $images->delete($image);

        return back()->with('status', 'Foto dihapus.');
    }

    public function reorderImages(Request $request, Property $kost, PropertyImageService $images): RedirectResponse
    {
        abort_unless($kost->owner_id === $request->user()->id, 403);
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer'],
        ]);
        $images->reorder($kost, $data['order']);

        return back()->with('status', 'Urutan foto disimpan.');
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;
        while (Property::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
