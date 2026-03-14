@php
    $mobileLinks = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Aukcije', 'route' => 'admin.auctions.index'],
        ['label' => 'Korisnici', 'route' => 'admin.users.index'],
        ['label' => 'KYC pregled', 'route' => 'admin.kyc.index'],
        ['label' => 'Kategorije', 'route' => 'admin.categories.index'],
        ['label' => 'Sporovi', 'route' => 'admin.disputes.index'],
        ['label' => 'Statične stranice', 'route' => 'admin.content.pages.index'],
        ['label' => 'Vijesti', 'route' => 'admin.content.news.index'],
        ['label' => 'Audit log', 'route' => 'admin.activity.index'],
        ['label' => 'Statistike', 'route' => 'admin.statistics'],
    ];
@endphp

<header x-data="{ mobileAdminNav: false }" class="border-b border-slate-200/70 bg-white/80 px-4 py-4 backdrop-blur sm:px-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="text-sm font-medium uppercase tracking-[0.18em] text-slate-500">Moderacija i operacije</p>
            <h1 class="text-xl font-semibold text-slate-900">@yield('admin_heading', 'Admin Dashboard')</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <span class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1 text-sm font-medium text-amber-800">Sporovi pod pažnjom</span>
            <span class="rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-sm font-medium text-sky-800">KYC red aktivan</span>
            <a href="{{ route('home') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                Otvori storefront
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center rounded-xl bg-slate-950 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                    Odjava
                </button>
            </form>
            <button type="button" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 lg:hidden" @click="mobileAdminNav = !mobileAdminNav">
                Meni
            </button>
        </div>
    </div>

    <div x-show="mobileAdminNav" x-transition class="mt-4 grid gap-2 border-t border-slate-200 pt-4 lg:hidden" style="display: none;">
        @foreach ($mobileLinks as $link)
            <a href="{{ route($link['route']) }}" class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                {{ $link['label'] }}
            </a>
        @endforeach
    </div>
</header>
