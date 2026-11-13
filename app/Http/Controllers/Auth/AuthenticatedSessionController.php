<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse|JsonResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->is_active) {
            auth()->logout();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akun Anda ditangguhkan.',
                    'errors' => ['email' => ['Akun Anda ditangguhkan.']],
                ], 422);
            }

            return back()->withErrors(['email' => 'Akun Anda ditangguhkan.']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $request->session()->pull('url.intended', route($user->role->dashboardRoute())),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->value,
                ],
            ]);
        }

        return redirect()->intended(route($user->role->dashboardRoute()));
    }

    public function destroy(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
