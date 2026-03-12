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
            'role' => ['required', 'in:buyer,seller'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role
        $user->assignRole($request->role);

        // Create profile
        UserProfile::create(['user_id' => $user->id, 'full_name' => $request->name]);

        // Create wallet
        Wallet::create(['user_id' => $user->id]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        return redirect()->intended($request->role === 'seller' ? '/seller/dashboard' : '/dashboard');
    }
}
