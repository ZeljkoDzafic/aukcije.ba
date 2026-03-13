<aside class="hidden w-72 flex-shrink-0 border-r border-slate-200 bg-slate-950 text-slate-100 lg:block">
    <div class="border-b border-slate-800 px-6 py-5 text-lg font-semibold">Admin Panel</div>
    <nav class="space-y-2 p-4">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Dashboard</a>
        <a href="{{ route('admin.auctions.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Aukcije</a>
        <a href="{{ route('admin.users.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Korisnici</a>
        <a href="{{ route('admin.kyc.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">KYC pregled</a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Kategorije</a>
        <a href="{{ route('admin.disputes.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Sporovi</a>
        <a href="{{ route('admin.content.pages.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Statične stranice</a>
        <a href="{{ route('admin.content.news.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Vijesti</a>
        <a href="{{ route('admin.activity.index') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Audit log</a>
        <a href="{{ route('admin.statistics') }}" class="sidebar-link text-slate-200 hover:bg-slate-800 hover:text-white">Statistike</a>
    </nav>
</aside>
