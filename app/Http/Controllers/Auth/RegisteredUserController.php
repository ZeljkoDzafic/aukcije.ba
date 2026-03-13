<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', 'in:buyer,seller'],
            'marketplace_focus' => ['nullable', 'in:buyer,seller'],
        ]);

        $focus = $request->string('marketplace_focus')->toString();
        if (! in_array($focus, ['buyer', 'seller'], true)) {
            $focus = $request->string('role')->toString();
        }
        if (! in_array($focus, ['buyer', 'seller'], true)) {
            $focus = 'buyer';
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('buyer');

        if ($focus === 'seller') {
            $user->assignRole('seller');
        }

        $profilePayload = [
            'user_id' => $user->id,
            'full_name' => $request->name,
        ];

        if (Schema::hasColumn('user_profiles', 'preferred_language')) {
            $profilePayload['preferred_language'] = 'sr';
        }

        if (Schema::hasColumn('user_profiles', 'primary_marketplace_focus')) {
            $profilePayload['primary_marketplace_focus'] = $focus;
        }

        UserProfile::create($profilePayload);

        Wallet::create(['user_id' => $user->id]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route($user->preferredHomeRoute()));
    }
}
