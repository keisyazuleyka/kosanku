<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        $user = auth()->user();
        $view = match (true) {
            $user->isAdmin() => 'admin.profile',
            $user->isOwner() => 'owner.profile',
            default => 'tenant.profile',
        };

        return view($view, ['user' => $user]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar_path'] = $path;
        }

        $user->fill($validated);
        $user->save();

        return back()->with('status', 'Profil berhasil diperbarui. Role tidak dapat diubah dari halaman ini.');
    }
}
