<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function edit(): View
    {
        $keys = [
            'site_name', 'tagline', 'contact_email', 'contact_phone',
            'min_listing_price', 'how_it_works', 'terms', 'privacy',
        ];

        $settings = PlatformSetting::query()->whereIn('key', $keys)->pluck('value', 'key');

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request, AuditLogger $logger): RedirectResponse
    {
        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:80'],
            'tagline' => ['required', 'string', 'max:160'],
            'contact_email' => ['required', 'email'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'min_listing_price' => ['required', 'integer', 'min:0'],
            'how_it_works' => ['nullable', 'string'],
            'terms' => ['nullable', 'string'],
            'privacy' => ['nullable', 'string'],
        ]);

        foreach ($data as $key => $value) {
            $group = in_array($key, ['how_it_works', 'terms', 'privacy'], true) ? 'content' : 'general';
            PlatformSetting::put($key, $value, $group);
        }

        $logger->record($request->user(), 'settings.updated', null, ['keys' => array_keys($data)]);

        return back()->with('status', 'Pengaturan disimpan.');
    }
}
