<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FacilityController extends Controller
{
    public function index(): View
    {
        return view('admin.facilities.index', [
            'facilities' => Facility::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'icon' => ['nullable', 'string', 'max:40'],
        ]);

        Facility::query()->updateOrCreate(
            ['slug' => Str::slug($data['name'])],
            ['name' => $data['name'], 'icon' => $data['icon'] ?? null, 'is_active' => true]
        );

        return back()->with('status', 'Fasilitas disimpan.');
    }

    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->delete();

        return back()->with('status', 'Fasilitas dihapus.');
    }
}
