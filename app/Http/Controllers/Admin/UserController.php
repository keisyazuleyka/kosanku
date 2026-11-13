<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->string('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status') === 'active');
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(20)->withQueryString(),
            'roles' => UserRole::cases(),
        ]);
    }

    public function show(User $user): View
    {
        $user->load(['properties', 'bookings.property', 'ownerBookings.property']);

        return view('admin.users.show', compact('user'));
    }

    public function toggle(Request $request, User $user, AuditLogger $logger): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => 'Anda tidak dapat menangguhkan akun sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $logger->record($request->user(), $user->is_active ? 'user.activated' : 'user.suspended', $user, [
            'email' => $user->email,
        ]);

        return back()->with('status', $user->is_active ? 'Akun diaktifkan.' : 'Akun ditangguhkan.');
    }
}
