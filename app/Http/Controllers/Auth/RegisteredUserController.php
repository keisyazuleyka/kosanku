<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse|JsonResponse
    {
        $role = UserRole::from($request->validated('role'));

        if ($role === UserRole::Admin) {
            abort(403);
        }

        $user = new User;
        $user->fill($request->safe()->only(['name', 'email', 'phone', 'password']));
        $user->role = $role;
        $user->is_active = true;
        $user->save();

        event(new Registered($user));
        Auth::login($user);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route($user->role->dashboardRoute()),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->value,
                ],
            ]);
        }

        return redirect()->route($user->role->dashboardRoute());
    }
}
