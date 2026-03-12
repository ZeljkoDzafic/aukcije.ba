@extends('layouts.guest')

@section('title', 'Prijava')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-md space-y-6">
        <div class="space-y-3 text-center">
            <x-badge variant="trust">Povratak na platformu</x-badge>
            <h1 class="text-3xl font-semibold text-slate-900">Prijavi se na svoj račun</h1>
            <p class="text-slate-600">Kupci nastavljaju na dashboard, selleri u seller studio, a administracija u moderacijski panel.</p>
        </div>

        <x-card class="space-y-5">
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <x-input name="email" type="email" label="Email adresa" :value="old('email')" placeholder="ime@primjer.ba" />
                @error('email')
                    <p class="-mt-3 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <x-input name="password" type="password" label="Lozinka" placeholder="••••••••" />
                @error('password')
                    <p class="-mt-3 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="flex items-center justify-between text-sm">
                    <label class="inline-flex items-center gap-2 text-slate-600">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-trust-600 focus:ring-trust-500">
                        <span>Zapamti me</span>
                    </label>
                    <a href="{{ url('/forgot-password') }}" class="link">Zaboravili ste lozinku?</a>
                </div>

                <div class="space-y-3">
                    <x-button class="w-full" type="submit">Prijavi se</x-button>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        Demo test korisnici koriste lozinku <span class="font-semibold text-slate-900">Test12345!</span>.
                    </div>
                </div>
            </form>
        </x-card>

        <p class="text-center text-sm text-slate-600">Nemaš račun? <a href="{{ route('register') }}" class="link">Registruj se</a></p>
    </div>
</section>
@endsection
