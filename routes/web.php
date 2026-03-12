<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\FeatureFlagController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Models\AdminLog;
use App\Models\Auction;
use App\Models\AuctionNotification;
use App\Models\Category;
use App\Models\ContentPage;
use App\Models\Message;
use App\Models\NewsArticle;
use App\Models\Order;
use App\Models\SavedSearch;
use App\Models\User;
use App\Models\UserRating;
use App\Services\AdminAuditService;
use App\Services\MarketplaceNotificationService;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes - Public Pages
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    $featuredAuctions = collect([
        ['id' => 1, 'title' => 'Rolex Oyster Perpetual 39', 'category' => 'Satovi', 'price' => 4250.00, 'bids' => 18, 'watchers' => 57, 'location' => 'Sarajevo', 'time' => '02d 14h', 'image_url' => null],
        ['id' => 2, 'title' => 'Bosanski cilim iz 1950.', 'category' => 'Kolekcionarstvo', 'price' => 620.00, 'bids' => 9, 'watchers' => 21, 'location' => 'Travnik', 'time' => '1d 09h', 'image_url' => null],
        ['id' => 3, 'title' => 'Canon EOS R6 sa objektivom', 'category' => 'Foto oprema', 'price' => 1890.00, 'bids' => 22, 'watchers' => 58, 'location' => 'Mostar', 'time' => '3d 01h', 'image_url' => null],
    ]);
    $featuredAuction = $featuredAuctions->first();
    $categoryHighlights = collect([
        ['name' => 'Elektronika', 'slug' => 'elektronika', 'count' => 24],
        ['name' => 'Satovi', 'slug' => 'vintage-satovi', 'count' => 11],
        ['name' => 'Automobili i vozila', 'slug' => 'automobili-i-vozila', 'count' => 7],
        ['name' => 'Kolekcionarstvo', 'slug' => 'kolekcionarstvo', 'count' => 9],
    ]);
    $latestNews = collect([
        ['slug' => 'sigurnosni-savjeti-za-kupce', 'title' => 'Sigurnosni savjeti za kupce i prodavače', 'excerpt' => 'Kako provjeriti artikl, komunikaciju i rokove isporuke prije završetka transakcije.', 'published_at' => now()->subDays(2)],
        ['slug' => 'nova-pravila-za-verifikovane-prodavace', 'title' => 'Nova pravila za verifikovane prodavače', 'excerpt' => 'Pregled KYC procesa, dokumenata i obaveza za profesionalne naloge.', 'published_at' => now()->subDays(5)],
    ]);
    $popularSearches = collect(['Rolex', 'iPhone', 'Gramofon', 'Lego set', 'Canon', 'Zlato']);
    $trendingCategories = collect([
        ['name' => 'Satovi', 'slug' => 'vintage-satovi', 'count' => 11],
        ['name' => 'Elektronika', 'slug' => 'elektronika', 'count' => 24],
        ['name' => 'Foto oprema', 'slug' => 'foto-oprema', 'count' => 8],
        ['name' => 'Kolekcionarstvo', 'slug' => 'kolekcionarstvo', 'count' => 9],
    ]);
    $mostWatchedAuctions = $featuredAuctions;
    $endingSoonAuctions = $featuredAuctions;

    if (Schema::hasTable('auctions')) {
        $databaseFeatured = Auction::query()
            ->with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->orderByDesc('is_featured')
            ->orderBy('ends_at')
            ->limit(3)
            ->get()
            ->map(fn (Auction $auction) => [
                'id' => $auction->id,
                'title' => $auction->title,
                'category' => $auction->category?->name ?? 'Bez kategorije',
                'price' => (float) $auction->current_price,
                'bids' => $auction->bids_count,
                'watchers' => $auction->watchers_count,
                'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                'time' => $auction->time_remaining,
                'image_url' => $auction->primaryImage?->url,
            ]);

        if ($databaseFeatured->isNotEmpty()) {
            $featuredAuctions = $databaseFeatured;
            $featuredAuction = $databaseFeatured->first();
        }

        $databaseMostWatched = Auction::query()
            ->with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->orderByDesc('watchers_count')
            ->orderByDesc('bids_count')
            ->limit(3)
            ->get()
            ->map(fn (Auction $auction) => [
                'id' => $auction->id,
                'title' => $auction->title,
                'category' => $auction->category?->name ?? 'Bez kategorije',
                'price' => (float) $auction->current_price,
                'bids' => $auction->bids_count,
                'watchers' => $auction->watchers_count,
                'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                'time' => $auction->time_remaining,
                'image_url' => $auction->primaryImage?->url,
            ]);

        if ($databaseMostWatched->isNotEmpty()) {
            $mostWatchedAuctions = $databaseMostWatched;
        }

        $databaseEndingSoon = Auction::query()
            ->with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->orderBy('ends_at')
            ->limit(3)
            ->get()
            ->map(fn (Auction $auction) => [
                'id' => $auction->id,
                'title' => $auction->title,
                'category' => $auction->category?->name ?? 'Bez kategorije',
                'price' => (float) $auction->current_price,
                'bids' => $auction->bids_count,
                'watchers' => $auction->watchers_count,
                'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                'time' => $auction->time_remaining,
                'image_url' => $auction->primaryImage?->url,
            ]);

        if ($databaseEndingSoon->isNotEmpty()) {
            $endingSoonAuctions = $databaseEndingSoon;
        }
    }

    if (Schema::hasTable('categories')) {
        $databaseCategories = Category::query()
            ->withCount('auctions')
            ->whereNull('parent_id')
            ->orderByDesc('auctions_count')
            ->limit(4)
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => (int) $category->auctions_count,
            ]);

        if ($databaseCategories->isNotEmpty()) {
            $categoryHighlights = $databaseCategories;
        }

        $databaseTrending = Category::query()
            ->withCount('auctions')
            ->orderByDesc('auctions_count')
            ->limit(4)
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'count' => (int) $category->auctions_count,
            ]);

        if ($databaseTrending->isNotEmpty()) {
            $trendingCategories = $databaseTrending;
        }
    }

    if (Schema::hasTable('news_articles')) {
        $databaseNews = NewsArticle::query()
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get()
            ->map(fn (NewsArticle $article) => [
                'slug' => $article->slug,
                'title' => $article->title,
                'excerpt' => $article->excerpt,
                'published_at' => $article->published_at ?? $article->created_at,
            ]);

        if ($databaseNews->isNotEmpty()) {
            $latestNews = $databaseNews;
        }
    }

    return view('pages.home', compact('featuredAuctions', 'featuredAuction', 'categoryHighlights', 'latestNews', 'popularSearches', 'trendingCategories', 'mostWatchedAuctions', 'endingSoonAuctions'));
})->name('home');

Route::get('/health', function () {
    $checks = [
        'app' => true,
        'database' => true,
        'cache' => true,
    ];

    try {
        DB::select('select 1');
    } catch (Throwable) {
        $checks['database'] = false;
    }

    try {
        Cache::put('healthcheck', true, 5);
    } catch (Throwable) {
        $checks['cache'] = false;
    }

    $healthy = ! in_array(false, $checks, true);

    return response()->json([
        'status' => $healthy ? 'healthy' : 'degraded',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ], $healthy ? 200 : 503);
})->name('health');

Route::middleware(['auth', 'role:admin|moderator|super_admin'])->get('/health/detailed', function () {
    $checks = [
        'database' => true,
        'cache' => true,
        'queue_default' => config('queue.default'),
        'broadcast_default' => config('broadcasting.default'),
        'scout_driver' => config('scout.driver'),
    ];

    try {
        DB::select('select 1');
    } catch (Throwable) {
        $checks['database'] = false;
    }

    try {
        Cache::put('healthcheck_detailed', true, 5);
    } catch (Throwable) {
        $checks['cache'] = false;
    }

    return response()->json([
        'status' => ! in_array(false, $checks, true) ? 'healthy' : 'degraded',
        'checks' => $checks,
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health.detailed');

Route::get('/robots.txt', function () {
    return response("User-agent: *\nAllow: /\nSitemap: ".url('/sitemap.xml')."\n", 200, [
        'Content-Type' => 'text/plain; charset=UTF-8',
    ]);
})->name('robots');

Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'),
        route('auctions.index'),
        route('categories.index'),
        route('search'),
        route('login'),
        route('register'),
    ];

    if (Schema::hasTable('categories')) {
        $urls = array_merge(
            $urls,
            Category::query()
                ->whereNotNull('slug')
                ->limit(100)
                ->pluck('slug')
                ->map(fn (string $slug) => route('categories.show', ['category' => $slug]))
                ->all()
        );
    }

    if (Schema::hasTable('auctions')) {
        $urls = array_merge(
            $urls,
            Auction::query()
                ->where('status', 'active')
                ->limit(200)
                ->pluck('id')
                ->map(fn (string $id) => route('auctions.show', ['auction' => $id]))
                ->all()
        );
    }

    $xml = view('seo.sitemap', [
        'urls' => collect($urls)->unique()->values()->map(fn (string $url) => [
            'loc' => $url,
            'lastmod' => now()->toDateString(),
        ]),
    ])->render();

    return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
})->name('sitemap');

// Auctions listing
Route::get('/aukcije', function () {
    return view('pages.auctions.index');
})->name('auctions.index');

// Auction detail
Route::get('/aukcije/{auction}', function () {
    $fallbackAuction = (object) [
        'id' => 1,
        'title' => 'Samsung Galaxy S24 Ultra 512GB',
        'description' => 'Odlično očuvan uređaj sa kompletnom opremom, originalnom kutijom i još 8 mjeseci garancije.',
        'category_name' => 'Elektronika',
        'category_slug' => 'elektronika',
        'current_price' => 1250.00,
        'minimum_bid' => 1255.00,
        'ends_at' => now()->addDays(3),
        'location' => 'Sarajevo',
        'condition' => 'Polovno, odlično',
        'shipping_info' => 'Brza pošta ili lično preuzimanje',
        'bids_count' => 23,
        'watchers_count' => 32,
        'seller_name' => 'Haris B.',
        'seller_rating' => '4.9',
        'seller_sales' => 128,
        'seller_location' => 'Sarajevo',
    ];

    $auctionRecord = null;

    if (Schema::hasTable('auctions')) {
        $auctionRecord = Auction::query()
            ->with(['category', 'seller', 'bids.user', 'images'])
            ->find(request()->route('auction'));
    }

    $auction = $auctionRecord
        ? (object) [
            'id' => $auctionRecord->id,
            'title' => $auctionRecord->title,
            'description' => $auctionRecord->description ?: $fallbackAuction->description,
            'category_name' => $auctionRecord->category?->name ?? 'Bez kategorije',
            'category_slug' => $auctionRecord->category?->slug ?? 'bez-kategorije',
            'current_price' => (float) $auctionRecord->current_price,
            'minimum_bid' => (float) $auctionRecord->minimum_bid,
            'ends_at' => $auctionRecord->ends_at,
            'location' => $auctionRecord->location_city ?? $auctionRecord->location ?? 'Nepoznato',
            'condition' => (string) $auctionRecord->condition,
            'shipping_info' => $auctionRecord->shipping_info ?: 'Dostava dostupna prema dogovoru',
            'bids_count' => $auctionRecord->bids_count,
            'watchers_count' => $auctionRecord->watchers_count,
            'seller_name' => $auctionRecord->seller?->name ?? 'Nepoznato',
            'seller_id' => $auctionRecord->seller?->id,
            'seller_rating' => number_format((float) ($auctionRecord->seller?->trust_score ?? 4.8), 1),
            'seller_sales' => $auctionRecord->seller?->auctions()->count() ?? 0,
            'seller_location' => $auctionRecord->seller?->profile?->city ?? ($auctionRecord->location_city ?? 'BiH'),
            'images' => $auctionRecord->images->pluck('url')->all(),
            'bid_history' => $auctionRecord->bids->sortByDesc('amount')->take(5)->map(fn ($bid) => [
                'bidder' => substr($bid->user?->name ?? 'anon', 0, 1).'***'.substr($bid->user?->name ?? '0', -1),
                'amount' => number_format((float) $bid->amount, 2, ',', '.').' BAM',
            ])->values()->all(),
        ]
        : $fallbackAuction;

    $relatedAuctions = collect();

    if ($auctionRecord?->category_id && Schema::hasTable('auctions')) {
        $relatedAuctions = Auction::query()
            ->with('category')
            ->where('category_id', $auctionRecord->category_id)
            ->whereKeyNot($auctionRecord->id)
            ->where('status', 'active')
            ->limit(4)
            ->get()
            ->map(fn (Auction $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'category' => $item->category?->name ?? 'Bez kategorije',
                'price' => (float) $item->current_price,
                'bids' => $item->bids_count,
                'watchers' => $item->watchers_count,
                'location' => $item->location_city ?? $item->location ?? 'Nepoznato',
                'time' => $item->time_remaining,
            ]);
    }

    if ($relatedAuctions->isEmpty()) {
        $relatedAuctions = collect([
            ['id' => 2, 'title' => 'Google Pixel 9 Pro', 'category' => 'Elektronika', 'price' => 1320.00, 'bids' => 10, 'watchers' => 20, 'location' => 'Tuzla', 'time' => '1d 02h'],
            ['id' => 3, 'title' => 'iPhone 15 Pro Max', 'category' => 'Elektronika', 'price' => 1980.00, 'bids' => 21, 'watchers' => 49, 'location' => 'Mostar', 'time' => '8h 32m'],
            ['id' => 4, 'title' => 'OnePlus 13', 'category' => 'Elektronika', 'price' => 950.00, 'bids' => 7, 'watchers' => 18, 'location' => 'Zenica', 'time' => '2d 01h'],
            ['id' => 5, 'title' => 'Xiaomi 15 Ultra', 'category' => 'Elektronika', 'price' => 1210.00, 'bids' => 9, 'watchers' => 27, 'location' => 'Sarajevo', 'time' => '3d 06h'],
        ]);
    }

    return view('pages.auctions.show', [
        'auction' => $auction,
        'relatedAuctions' => $relatedAuctions,
    ]);
})->name('auctions.show');

// Categories
Route::get('/kategorije', function () {
    $categories = collect([
        [
            'name' => 'Elektronika',
            'slug' => 'elektronika',
            'icon' => 'device-phone-mobile',
            'auctions_count' => 24,
            'children' => [
                ['name' => 'Mobiteli i tableti', 'slug' => 'mobiteli-i-tableti', 'auctions_count' => 8],
                ['name' => 'Laptopi i računari', 'slug' => 'laptopi-i-racunari', 'auctions_count' => 6],
                ['name' => 'TV i audio', 'slug' => 'tv-i-audio', 'auctions_count' => 5],
            ],
        ],
        [
            'name' => 'Kolekcionarstvo',
            'slug' => 'kolekcionarstvo',
            'icon' => 'star',
            'auctions_count' => 11,
            'children' => [
                ['name' => 'Vintage satovi', 'slug' => 'vintage-satovi', 'auctions_count' => 4],
                ['name' => 'Stari novac i marke', 'slug' => 'stari-novac-i-marke', 'auctions_count' => 3],
            ],
        ],
    ]);

    if (Schema::hasTable('categories')) {
        $databaseCategories = Category::query()
            ->withCount('auctions')
            ->with(['children' => fn ($query) => $query->withCount('auctions')->orderBy('sort_order')->orderBy('name')])
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn (Category $category) => [
                'name' => $category->name,
                'slug' => $category->slug,
                'icon' => $category->icon,
                'auctions_count' => (int) $category->auctions_count,
                'children' => $category->children->map(fn (Category $child) => [
                    'name' => $child->name,
                    'slug' => $child->slug,
                    'auctions_count' => $child->auctions_count ?? 0,
                ])->all(),
            ]);

        if ($databaseCategories->isNotEmpty()) {
            $categories = $databaseCategories;
        }
    }

    return view('pages.categories.index', ['categories' => $categories]);
})->name('categories.index');

Route::get('/kategorije/{category}', function () {
    $slug = (string) request()->route('category');

    $categoryRecord = Schema::hasTable('categories')
        ? Category::query()->with(['children' => fn ($query) => $query->withCount('auctions')->orderBy('sort_order')->orderBy('name'), 'parent'])->where('slug', $slug)->withCount('auctions')->first()
        : null;

    $category = (object) [
        'name' => $categoryRecord?->name ?? str($slug)->replace('-', ' ')->title()->value(),
        'slug' => $categoryRecord?->slug ?? $slug,
        'description' => 'Pregled tržišta, najtraženijih artikala i aukcija koje završavaju uskoro.',
        'auctions_count' => $categoryRecord?->auctions_count ?? 3,
        'parent_name' => $categoryRecord?->parent?->name,
        'parent_slug' => $categoryRecord?->parent?->slug,
        'children' => collect($categoryRecord?->children ?? [])->map(fn (Category $child) => [
            'name' => $child->name,
            'slug' => $child->slug,
            'auctions_count' => $child->auctions_count ?? 0,
        ]),
    ];

    $auctions = collect([
        ['id' => 1, 'title' => 'Samsung Galaxy S24 Ultra', 'category' => $category->name, 'price' => 1250.00, 'bids' => 14, 'watchers' => 32, 'location' => 'Sarajevo', 'time' => '2d 04h', 'image_url' => null],
        ['id' => 2, 'title' => 'Sony WH-1000XM5', 'category' => $category->name, 'price' => 480.00, 'bids' => 11, 'watchers' => 17, 'location' => 'Banja Luka', 'time' => '5h 12m', 'image_url' => null],
        ['id' => 3, 'title' => 'Nintendo Switch OLED', 'category' => $category->name, 'price' => 410.00, 'bids' => 8, 'watchers' => 29, 'location' => 'Zenica', 'time' => '9h 48m', 'image_url' => null],
    ]);

    if ($categoryRecord && Schema::hasTable('auctions')) {
        $categoryIds = collect([$categoryRecord->id])
            ->merge($categoryRecord->children->pluck('id'))
            ->all();

        $databaseAuctions = Auction::query()
            ->with(['category', 'primaryImage'])
            ->whereIn('category_id', $categoryIds)
            ->where('status', 'active')
            ->limit(6)
            ->get()
            ->map(fn (Auction $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'category' => $item->category?->name ?? $category->name,
                'price' => (float) $item->current_price,
                'bids' => $item->bids_count,
                'watchers' => $item->watchers_count,
                'location' => $item->location_city ?? $item->location ?? 'Nepoznato',
                'time' => $item->time_remaining,
                'image_url' => $item->primaryImage?->url,
            ]);

        if ($databaseAuctions->isNotEmpty()) {
            $auctions = $databaseAuctions;
        }
    }

    return view('pages.categories.show', compact('category', 'auctions'));
})->name('categories.show');

// Search
Route::get('/pretraga', function () {
    $query = trim((string) request()->string('q'));
    $selectedCategory = request()->string('category')->toString();

    $categories = collect();
    $results = collect();

    if (Schema::hasTable('categories')) {
        $categories = Category::query()
            ->active()
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
    }

    if ($query !== '' && Schema::hasTable('auctions')) {
        $resultsQuery = Auction::query()
            ->with(['category', 'primaryImage'])
            ->where('status', 'active')
            ->where(function ($builder) use ($query) {
                $builder->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('location', 'like', "%{$query}%");
            });

        if ($selectedCategory !== '' && Schema::hasTable('categories')) {
            $category = Category::query()->where('slug', $selectedCategory)->first();

            if ($category) {
                $categoryIds = collect([$category->id]);

                if (method_exists($category, 'children')) {
                    $categoryIds = $categoryIds->merge($category->children()->pluck('id'));
                }

                $resultsQuery->whereIn('category_id', $categoryIds->all());
            }
        }

        $results = $resultsQuery
            ->orderByDesc('is_featured')
            ->orderBy('ends_at')
            ->limit(18)
            ->get()
            ->map(fn (Auction $auction) => [
                'id' => $auction->id,
                'title' => $auction->title,
                'category' => $auction->category?->name ?? 'Bez kategorije',
                'price' => (float) $auction->current_price,
                'bids' => $auction->bids_count,
                'watchers' => $auction->watchers_count,
                'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                'time' => $auction->time_remaining,
                'image_url' => $auction->primaryImage?->url,
                'badge' => $auction->ends_at && $auction->ends_at->diffInHours(now(), false) <= 24 ? 'Uskoro završava' : 'Aktivna aukcija',
            ]);
    }

    return view('pages.search', [
        'query' => $query,
        'selectedCategory' => $selectedCategory,
        'categories' => $categories,
        'results' => $results,
    ]);
})->name('search');

Route::get('/o-nama', function () {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', 'o-nama')->first()
        : null;

    return view('pages.content.show', [
        'page' => $page ?? (object) [
            'title' => 'O nama',
            'excerpt' => 'Aukcije.ba vodi Techentis s.p. kao odgovorni subjekt platforme.',
            'body' => "<p>Aukcije.ba je aukcijska platforma za sigurno povezivanje kupaca i prodavača u regiji. Odgovorni subjekt za rad platforme je <strong>Techentis s.p.</strong>.</p><p>Naš fokus je sigurno licitiranje, jasna pravila objave, escrow zaštita i transparentna komunikacija tokom cijelog procesa kupovine i prodaje.</p>",
        ],
    ]);
})->name('content.about');

Route::get('/uvjeti-koristenja', function () {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', 'uvjeti-koristenja')->first()
        : null;

    return view('pages.content.show', [
        'page' => $page ?? (object) [
            'title' => 'Uslovi korištenja',
            'excerpt' => 'Pravila pristupa platformi, objave aukcija, licitiranja i završetka transakcija.',
            'body' => "<p>Korištenjem platforme prihvatate obavezu istinitog predstavljanja artikla, poštovanja rokova uplate i dostave, te zabranu manipulacije licitacijom.</p><p>Techentis s.p. zadržava pravo uklanjanja sadržaja, ograničenja naloga i moderacije spornih transakcija radi zaštite korisnika i integriteta tržišta.</p>",
        ],
    ]);
})->name('content.terms');

Route::get('/politika-privatnosti', function () {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', 'politika-privatnosti')->first()
        : null;

    return view('pages.content.show', [
        'page' => $page ?? (object) [
            'title' => 'Politika privatnosti',
            'excerpt' => 'Način prikupljanja, obrade i zaštite podataka korisnika platforme.',
            'body' => "<p>Techentis s.p. obrađuje podatke nužne za registraciju, verifikaciju, transakcije i sigurnost naloga. Podaci se koriste radi izvršenja usluge, sprečavanja zloupotreba i ispunjavanja zakonskih obaveza.</p><p>Korisnik može tražiti uvid, ispravku ili brisanje podataka u mjeri dopuštenoj zakonom i operativnim obavezama platforme.</p>",
        ],
    ]);
})->name('content.privacy');

Route::get('/kako-kupovati', function () {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', 'kako-kupovati')->first()
        : null;

    return view('pages.content.show', [
        'page' => $page ?? (object) [
            'title' => 'Kako kupovati',
            'excerpt' => 'Koraci od registracije do preuzimanja artikla.',
            'body' => "<p>Provjerite opis i fotografije artikla, pratite minimalni iznos sljedeće ponude i koristite poruke za dodatna pitanja prodavaču.</p><p>Nakon osvajanja aukcije pratite rok za uplatu, status narudžbe i podatke o dostavi unutar svog korisničkog naloga.</p>",
        ],
    ]);
})->name('content.buying');

Route::get('/kako-prodavati', function () {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', 'kako-prodavati')->first()
        : null;

    return view('pages.content.show', [
        'page' => $page ?? (object) [
            'title' => 'Kako prodavati',
            'excerpt' => 'Pravila kvalitetne objave, verifikacije i ispunjenja narudžbi.',
            'body' => "<p>Objavite jasan naslov, tačan opis stanja i kvalitetne fotografije. Cijena i način dostave moraju biti precizno navedeni prije objave aukcije.</p><p>Nakon završetka aukcije pravovremeno potvrdite uplatu, unesite tracking podatke i isporučite artikal u dogovorenom roku.</p>",
        ],
    ]);
})->name('content.selling');

Route::get('/pomoc', function () {
    $helpPages = collect([
        (object) ['title' => 'Kako kupovati', 'slug' => 'kako-kupovati', 'excerpt' => 'Koraci od pretrage do završetka kupovine.', 'page_type' => 'help'],
        (object) ['title' => 'Kako prodavati', 'slug' => 'kako-prodavati', 'excerpt' => 'Objava aukcije, slike, cijena i slanje.', 'page_type' => 'help'],
        (object) ['title' => 'Sigurna kupovina', 'slug' => 'sigurna-kupovina', 'excerpt' => 'Savjeti za provjeru prodavača, poruka i dostave.', 'page_type' => 'help'],
        (object) ['title' => 'Ocjene i saradnja', 'slug' => 'ocjene-i-saradnja', 'excerpt' => 'Kako funkcionišu ocjene i reputacija na platformi.', 'page_type' => 'help'],
        (object) ['title' => 'Kontakt', 'slug' => 'kontakt', 'excerpt' => 'Kako kontaktirati Techentis s.p. i podršku platforme.', 'page_type' => 'company'],
    ]);

    if (Schema::hasTable('content_pages')) {
        $databasePages = ContentPage::query()
            ->published()
            ->whereIn('page_type', ['help', 'company'])
            ->orderBy('title')
            ->get();

        if ($databasePages->isNotEmpty()) {
            $helpPages = $databasePages;
        }
    }

    $sections = [
        'Kupovina' => ['Kako kupovati', 'Sigurna kupovina'],
        'Prodaja' => ['Kako prodavati'],
        'Nalog i povjerenje' => ['Ocjene i saradnja', 'Kontakt'],
    ];

    return view('pages.help.index', [
        'helpPages' => $helpPages,
        'sections' => $sections,
    ]);
})->name('help.index');

Route::get('/vijesti', function () {
    $articles = collect([
        (object) ['slug' => 'sigurnosni-savjeti-za-kupce', 'title' => 'Sigurnosni savjeti za kupce i prodavače', 'excerpt' => 'Kako prepoznati kvalitetnu aukciju i izbjeći nesporazume prije završetka transakcije.', 'published_at' => now()->subDays(2)],
        (object) ['slug' => 'nova-pravila-za-verifikovane-prodavace', 'title' => 'Nova pravila za verifikovane prodavače', 'excerpt' => 'Šta se mijenja u procesu provjere dokumenata i seller obavezama.', 'published_at' => now()->subDays(5)],
    ]);

    if (Schema::hasTable('news_articles')) {
        $databaseArticles = NewsArticle::query()
            ->published()
            ->latest('published_at')
            ->get();

        if ($databaseArticles->isNotEmpty()) {
            $articles = $databaseArticles;
        }
    }

    return view('pages.news.index', ['articles' => $articles]);
})->name('news.index');

Route::get('/vijesti/{article}', function () {
    $slug = (string) request()->route('article');

    $article = Schema::hasTable('news_articles')
        ? NewsArticle::query()->published()->where('slug', $slug)->first()
        : null;

    $article ??= (object) [
        'title' => str($slug)->replace('-', ' ')->title()->value(),
        'excerpt' => 'Službena obavijest platforme Aukcije.ba.',
        'body' => "<p>Ova objava služi kao informativna vijest za korisnike platforme Aukcije.ba i vodič kroz promjene proizvoda, pravila ili sigurnosne preporuke.</p>",
        'published_at' => now(),
    ];

    return view('pages.news.show', ['article' => $article]);
})->name('news.show');

Route::get('/prodavci/{user}', function (string $user) {
    $seller = Schema::hasTable('users')
        ? User::query()->with(['profile', 'auctions.category', 'auctions.primaryImage', 'ratingsReceived'])->find($user)
        : null;

    abort_if(! $seller, 404);

    $activeAuctions = $seller->auctions()
        ->with(['category', 'primaryImage'])
        ->where('status', 'active')
        ->limit(6)
        ->get()
        ->map(fn (Auction $auction) => [
            'id' => $auction->id,
            'title' => $auction->title,
            'category' => $auction->category?->name ?? 'Bez kategorije',
            'price' => (float) $auction->current_price,
            'bids' => $auction->bids_count,
            'watchers' => $auction->watchers_count,
            'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
            'time' => $auction->time_remaining,
            'image_url' => $auction->primaryImage?->url,
        ]);

    $featuredAuctions = $seller->auctions()
        ->with(['category', 'primaryImage'])
        ->where('status', 'active')
        ->orderByDesc('is_featured')
        ->orderByDesc('watchers_count')
        ->limit(2)
        ->get()
        ->map(fn (Auction $auction) => [
            'id' => $auction->id,
            'title' => $auction->title,
            'category' => $auction->category?->name ?? 'Bez kategorije',
            'price' => (float) $auction->current_price,
            'bids' => $auction->bids_count,
            'watchers' => $auction->watchers_count,
            'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
            'time' => $auction->time_remaining,
            'image_url' => $auction->primaryImage?->url,
        ]);

    $categoryCollections = $seller->auctions()
        ->with('category')
        ->where('status', 'active')
        ->get()
        ->groupBy(fn (Auction $auction) => $auction->category?->name ?? 'Bez kategorije')
        ->map(fn ($items, string $category) => [
            'category' => $category,
            'count' => $items->count(),
            'top_title' => $items->sortByDesc('watchers_count')->first()?->title ?? 'Aukcija',
        ])
        ->sortByDesc('count')
        ->take(4)
        ->values();

    $endingSoonAuctions = $seller->auctions()
        ->with(['category', 'primaryImage'])
        ->where('status', 'active')
        ->where('ends_at', '>', now())
        ->orderBy('ends_at')
        ->limit(3)
        ->get()
        ->map(fn (Auction $auction) => [
            'id' => $auction->id,
            'title' => $auction->title,
            'category' => $auction->category?->name ?? 'Bez kategorije',
            'price' => (float) $auction->current_price,
            'bids' => $auction->bids_count,
            'watchers' => $auction->watchers_count,
            'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
            'time' => $auction->time_remaining,
            'image_url' => $auction->primaryImage?->url,
        ]);

    $newlyListedAuctions = $seller->auctions()
        ->with(['category', 'primaryImage'])
        ->where('status', 'active')
        ->latest('created_at')
        ->limit(3)
        ->get()
        ->map(fn (Auction $auction) => [
            'id' => $auction->id,
            'title' => $auction->title,
            'category' => $auction->category?->name ?? 'Bez kategorije',
            'price' => (float) $auction->current_price,
            'bids' => $auction->bids_count,
            'watchers' => $auction->watchers_count,
            'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
            'time' => $auction->time_remaining,
            'image_url' => $auction->primaryImage?->url,
        ]);

    $ratings = UserRating::query()
        ->where(function ($query) use ($seller) {
            $query->where('rated_id', $seller->id);

            if (Schema::hasColumn('user_ratings', 'rated_user_id')) {
                $query->orWhere('rated_user_id', $seller->id);
            }
        })
        ->where('is_visible', true)
        ->latest('created_at')
        ->limit(5)
        ->get();

    $averageRating = $ratings->isNotEmpty()
        ? round((float) $ratings->avg('score'), 1)
        : (float) $seller->trust_score;

    return view('pages.sellers.show', [
        'seller' => $seller,
        'activeAuctions' => $activeAuctions,
        'featuredAuctions' => $featuredAuctions,
        'categoryCollections' => $categoryCollections,
        'endingSoonAuctions' => $endingSoonAuctions,
        'newlyListedAuctions' => $newlyListedAuctions,
        'ratings' => $ratings,
        'sellerStats' => [
            'trust' => number_format((float) $seller->trust_score, 1),
            'average_rating' => number_format($averageRating, 1),
            'ratings_count' => $ratings->count(),
            'active_auctions' => $seller->auctions()->where('status', 'active')->count(),
            'sold_orders' => $seller->soldOrders()->count(),
            'city' => $seller->profile?->city ?? 'BiH',
        ],
    ]);
})->name('sellers.show');

Route::get('/prodavci', function () {
    $search = request()->string('q')->toString();
    $sort = request()->string('sort')->toString() ?: 'reputation';

    $sellers = collect();

    if (Schema::hasTable('users')) {
        $sellers = User::query()
            ->with(['profile'])
            ->role(['seller', 'verified_seller'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhereHas('profile', fn ($profileQuery) => $profileQuery
                            ->where('city', 'like', '%'.$search.'%')
                            ->orWhere('bio', 'like', '%'.$search.'%'));
                });
            })
            ->get()
            ->map(function (User $seller): array {
                $ratingsCount = $seller->ratingsReceived()->visible()->count();
                $activeAuctions = $seller->auctions()->where('status', 'active')->count();

                return [
                    'id' => $seller->id,
                    'name' => $seller->name,
                    'city' => $seller->profile?->city ?? 'BiH',
                    'bio' => $seller->profile?->bio ?? 'Prodavač bez dodatnog opisa.',
                    'trust_score' => (float) $seller->trust_score,
                    'badge' => $seller->getTrustBadge(),
                    'active_auctions' => $activeAuctions,
                    'ratings_count' => $ratingsCount,
                    'sold_orders' => $seller->soldOrders()->count(),
                    'member_since' => optional($seller->created_at)->format('m/Y') ?? now()->format('m/Y'),
                ];
            });

        $sellers = match ($sort) {
            'activity' => $sellers->sortByDesc('active_auctions')->values(),
            'sales' => $sellers->sortByDesc('sold_orders')->values(),
            default => $sellers->sortByDesc('trust_score')->values(),
        };
    }

    return view('pages.sellers.index', [
        'sellers' => $sellers,
        'search' => $search,
        'sort' => $sort,
    ]);
})->name('sellers.index');

Route::get('/info/{slug}', function (string $slug) {
    $page = Schema::hasTable('content_pages')
        ? ContentPage::query()->published()->where('slug', $slug)->first()
        : null;

    abort_if(! $page, 404);

    return view('pages.content.show', ['page' => $page]);
})->name('content.show');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);

    Route::get('/forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    Route::get('/reset-password/{token?}', function () {
        return view('auth.reset-password');
    })->name('password.reset');

    Route::get('/two-factor-challenge', function () {
        return view('auth.two-factor-challenge');
    })->name('two-factor.login');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        $user = Auth::user();

        $stats = [
            ['label' => 'Aktivne ponude', 'value' => Schema::hasTable('bids') ? $user->bids()->whereHas('auction', fn ($query) => $query->where('status', 'active'))->count() : 0],
            ['label' => 'Dobijene aukcije', 'value' => Schema::hasTable('orders') ? $user->orders()->count() : 0],
            ['label' => 'Praćene aukcije', 'value' => Schema::hasTable('auction_watchers') ? $user->watchlist()->count() : 0],
            ['label' => 'Wallet balans', 'value' => Schema::hasTable('wallets') ? number_format((float) ($user->wallet?->balance ?? 0), 2, ',', '.').' BAM' : '0,00 BAM'],
        ];

        $activeBids = collect();
        if (Schema::hasTable('bids')) {
            $activeBids = $user->bids()
                ->with(['auction.category', 'auction.primaryImage'])
                ->whereHas('auction', fn ($query) => $query->where('status', 'active'))
                ->latest('created_at')
                ->limit(4)
                ->get()
                ->map(fn ($bid) => [
                    'id' => $bid->auction?->id ?? 'demo',
                    'title' => $bid->auction?->title ?? 'Aukcija',
                    'category' => $bid->auction?->category?->name ?? 'Bez kategorije',
                    'price' => (float) ($bid->auction?->current_price ?? $bid->amount),
                    'bids' => $bid->auction?->bids_count ?? 0,
                    'watchers' => $bid->auction?->watchers_count ?? 0,
                    'location' => $bid->auction?->location_city ?? $bid->auction?->location ?? 'Nepoznato',
                    'time' => $bid->auction?->time_remaining ?? 'Uskoro',
                    'image_url' => $bid->auction?->primaryImage?->url,
                ]);
        }

        if ($activeBids->isEmpty()) {
            $activeBids = collect([
                ['id' => 1, 'title' => 'MacBook Pro 14 M3', 'category' => 'Računari', 'price' => 2950.00, 'bids' => 17, 'watchers' => 43, 'location' => 'Sarajevo', 'time' => '11h 02m', 'image_url' => null],
                ['id' => 2, 'title' => 'Omega Seamaster', 'category' => 'Satovi', 'price' => 3820.00, 'bids' => 25, 'watchers' => 61, 'location' => 'Mostar', 'time' => '1d 16h', 'image_url' => null],
            ]);
        }

        $watchlistItems = Schema::hasTable('auction_watchers')
            ? $user->watchlist()->with('category')->limit(3)->get()->map(fn (Auction $auction) => "{$auction->title} zavrsava za {$auction->time_remaining}")
            : collect(['Vintage gramofon zavrsava za 2h', 'Canon R6 zavrsava sutra', 'Bicikl Trek - 7 bidova']);

        $orders = Schema::hasTable('orders')
            ? $user->orders()->with('auction')->latest()->limit(2)->get()->map(fn (Order $order) => [
                'title' => $order->auction?->title ?? "Narudzba {$order->id}",
                'status' => $order->status,
            ])
            : collect([
                ['title' => 'Fujifilm X-T5', 'status' => 'awaiting_shipment'],
                ['title' => 'Vinyl kolekcija', 'status' => 'paid'],
            ]);

        $bidSummary = [
            ['label' => 'Vodite', 'value' => Schema::hasTable('bids') ? $user->bids()->where('is_winning', true)->count() : 0],
            ['label' => 'Nadmašeni', 'value' => Schema::hasTable('bids') ? $user->bids()->where('is_winning', false)->count() : 0],
            ['label' => 'Osvojene aukcije', 'value' => Schema::hasTable('orders') ? $user->orders()->count() : 0],
        ];

        $commandCenter = [
            ['label' => 'Nepročitane poruke', 'value' => Schema::hasTable('messages') ? Message::query()->where('receiver_id', $user->id)->where('is_read', false)->count() : 0, 'href' => route('messages.index')],
            ['label' => 'Nove obavijesti', 'value' => Schema::hasTable('notifications_custom') ? $user->marketplaceNotifications()->whereNull('read_at')->count() : 0, 'href' => route('notifications.index')],
            ['label' => 'Spremljene pretrage', 'value' => Schema::hasTable('saved_searches') ? $user->savedSearches()->count() : 0, 'href' => route('searches.index')],
        ];

        $priorityQueue = [
            ['label' => 'Nadlicitirani ste', 'value' => Schema::hasTable('notifications_custom') ? $user->marketplaceNotifications()->where('type', 'outbid')->whereNull('read_at')->count() : 0, 'href' => route('notifications.index', ['filter' => 'auctions'])],
            ['label' => 'Plaćanje / isporuka', 'value' => Schema::hasTable('orders') ? $user->orders()->whereIn('status', ['pending_payment', 'paid', 'awaiting_shipment', 'shipped'])->count() : 0, 'href' => route('orders.index')],
            ['label' => 'Rok plaćanja uskoro', 'value' => Schema::hasTable('orders') ? $user->orders()->where('status', 'pending_payment')->whereNotNull('payment_deadline_at')->where('payment_deadline_at', '<=', now()->addDay())->count() : 0, 'href' => route('orders.index')],
            ['label' => 'Otvoreni sporovi', 'value' => Schema::hasTable('disputes') ? $user->orders()->whereHas('dispute', fn ($query) => $query->whereIn('status', ['open', 'escalated']))->count() : 0, 'href' => route('orders.index')],
            ['label' => 'Saved search pogodci', 'value' => Schema::hasTable('notifications_custom') ? $user->marketplaceNotifications()->where('type', 'saved_search_match')->whereNull('read_at')->count() : 0, 'href' => route('notifications.index', ['filter' => 'searches'])],
        ];

        return view('pages.dashboard', compact('stats', 'activeBids', 'watchlistItems', 'orders', 'bidSummary', 'commandCenter', 'priorityQueue'));
    })->name('dashboard');

    Route::get('/moje-licitacije', function () {
        $user = Auth::user();
        $status = (string) request('status', 'all');

        $bids = collect();

        if ($user && Schema::hasTable('bids')) {
            $bids = $user->bids()
                ->with(['auction.category', 'auction.primaryImage'])
                ->whereHas('auction')
                ->latest('created_at')
                ->get()
                ->unique('auction_id')
                ->map(function ($bid) {
                    $auction = $bid->auction;
                    $auctionStatus = (string) ($auction?->status ?? 'active');

                    $state = match (true) {
                        $auctionStatus !== 'active' => 'zavrseno',
                        (bool) $bid->is_winning => 'vodite',
                        default => 'nadmaseni',
                    };

                    return [
                        'id' => $auction?->id ?? 'demo',
                        'title' => $auction?->title ?? 'Aukcija',
                        'category' => $auction?->category?->name ?? 'Bez kategorije',
                        'price' => (float) ($auction?->current_price ?? $bid->amount),
                        'your_bid' => (float) $bid->amount,
                        'bids' => $auction?->bids_count ?? 0,
                        'watchers' => $auction?->watchers_count ?? 0,
                        'location' => $auction?->location_city ?? $auction?->location ?? 'Nepoznato',
                        'time' => $auction?->time_remaining ?? 'Završeno',
                        'image_url' => $auction?->primaryImage?->url,
                        'state' => $state,
                    ];
                })
                ->values();
        }

        $filteredBids = $status === 'all'
            ? $bids
            : $bids->where('state', $status)->values();

        return view('pages.bids.index', [
            'status' => $status,
            'filters' => [
                ['value' => 'all', 'label' => 'Sve', 'count' => $bids->count()],
                ['value' => 'vodite', 'label' => 'Vodite', 'count' => $bids->where('state', 'vodite')->count()],
                ['value' => 'nadmaseni', 'label' => 'Nadmašeni', 'count' => $bids->where('state', 'nadmaseni')->count()],
                ['value' => 'zavrseno', 'label' => 'Završeno', 'count' => $bids->where('state', 'zavrseno')->count()],
            ],
            'bids' => $filteredBids,
        ]);
    })->name('bids.index');

    // Watchlist
    Route::get('/watchlist', function () {
        return view('pages.watchlist.index');
    })->name('watchlist.index');

    // Profile
    Route::get('/profil', function () {
        $user = auth()->user()->load(['profile', 'wallet']);
        $ratingsCount = $user->ratingsReceived()->visible()->count();
        $activeSellerAuctions = $user->auctions()->where('status', 'active')->count();

        $profile = [
            'name' => $user->profile?->full_name ?? $user->name,
            'email' => $user->email,
            'city' => $user->profile?->city,
            'country' => $user->profile?->country,
            'phone' => $user->phone,
            'role_summary' => method_exists($user, 'roleSummary') ? $user->roleSummary() : 'buyer',
            'kyc_level' => $user->kycLevel(),
            'trust_score' => number_format((float) $user->trust_score, 1),
            'wallet_balance' => number_format((float) ($user->wallet?->balance ?? 0), 2, ',', '.').' BAM',
            'ratings_count' => $ratingsCount,
            'seller_badge' => $user->getTrustBadge(),
            'active_seller_auctions' => $activeSellerAuctions,
        ];

        return view('pages.profile.index', ['profile' => $profile]);
    })->name('profile.index');

    Route::get('/postavke/obavijesti', function () {
        $user = auth()->user();
        $preferences = array_merge([
            'email_outbid' => true,
            'email_auction_ended' => true,
            'email_ending_soon' => true,
            'email_messages' => true,
            'email_saved_searches' => true,
            'email_shipping_updates' => true,
            'email_disputes' => true,
            'sms_enabled' => false,
            'push_enabled' => true,
        ], $user->notification_preferences ?? []);

        return view('pages.settings.notifications', ['preferences' => $preferences]);
    })->name('settings.notifications');

    Route::post('/postavke/obavijesti', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'email_outbid' => ['nullable', 'boolean'],
            'email_auction_ended' => ['nullable', 'boolean'],
            'email_ending_soon' => ['nullable', 'boolean'],
            'email_messages' => ['nullable', 'boolean'],
            'email_saved_searches' => ['nullable', 'boolean'],
            'email_shipping_updates' => ['nullable', 'boolean'],
            'email_disputes' => ['nullable', 'boolean'],
            'sms_enabled' => ['nullable', 'boolean'],
            'push_enabled' => ['nullable', 'boolean'],
        ]);

        $user->forceFill([
            'notification_preferences' => [
                'email_outbid' => request()->boolean('email_outbid'),
                'email_auction_ended' => request()->boolean('email_auction_ended'),
                'email_ending_soon' => request()->boolean('email_ending_soon'),
                'email_messages' => request()->boolean('email_messages'),
                'email_saved_searches' => request()->boolean('email_saved_searches'),
                'email_shipping_updates' => request()->boolean('email_shipping_updates'),
                'email_disputes' => request()->boolean('email_disputes'),
                'sms_enabled' => request()->boolean('sms_enabled'),
                'push_enabled' => request()->boolean('push_enabled', true),
            ],
        ])->save();

        return redirect()->route('settings.notifications')->with('status', 'Postavke obavijesti su sačuvane.');
    })->name('settings.notifications.update');

    Route::get('/obavijesti', function () {
        $user = auth()->user();
        $filter = request()->string('filter')->toString() ?: 'all';
        $notifications = collect();

        if ($user && Schema::hasTable('notifications_custom')) {
            $notifications = app(MarketplaceNotificationService::class)->latestForUser($user, 50);
        }

        $sort = request()->string('sort')->toString() ?: 'priority';

        if ($filter !== 'all') {
            $notifications = $notifications->filter(function ($notification) use ($filter): bool {
                return match ($filter) {
                    'messages' => $notification->type === 'message',
                    'auctions' => in_array($notification->type, ['outbid', 'auction_won', 'auction_ending_soon'], true),
                    'shipping' => in_array($notification->type, ['shipment', 'item_shipped'], true),
                    'disputes' => in_array($notification->type, ['dispute_opened', 'dispute_updated', 'dispute_resolved'], true),
                    'searches' => $notification->type === 'saved_search_match',
                    default => true,
                };
            })->values();
        }

        $priorityMap = [
            'outbid' => 100,
            'auction_won' => 95,
            'shipment' => 90,
            'item_shipped' => 90,
            'dispute_opened' => 88,
            'dispute_updated' => 84,
            'dispute_resolved' => 82,
            'saved_search_match' => 70,
            'message' => 60,
            'auction_ending_soon' => 55,
        ];

        $notifications = match ($sort) {
            'latest' => $notifications
                ->sortByDesc(fn ($notification) => $notification->created_at?->getTimestamp() ?? 0)
                ->values(),
            'oldest' => $notifications
                ->sortBy(fn ($notification) => $notification->created_at?->getTimestamp() ?? 0)
                ->values(),
            default => $notifications
                ->sortByDesc(fn ($notification) => ($notification->read_at ? 0 : 1000) + ($priorityMap[$notification->type] ?? 10))
                ->values(),
        };

        $unreadByType = [
            'all' => $notifications->whereNull('read_at')->count(),
            'messages' => $notifications->where('type', 'message')->whereNull('read_at')->count(),
            'auctions' => $notifications->whereIn('type', ['outbid', 'auction_won', 'auction_ending_soon'])->whereNull('read_at')->count(),
            'shipping' => $notifications->whereIn('type', ['shipment', 'item_shipped'])->whereNull('read_at')->count(),
            'disputes' => $notifications->whereIn('type', ['dispute_opened', 'dispute_updated', 'dispute_resolved'])->whereNull('read_at')->count(),
            'searches' => $notifications->where('type', 'saved_search_match')->whereNull('read_at')->count(),
        ];

        $notifications = $notifications->map(function ($notification) {
            $data = is_array($notification->data) ? $notification->data : [];
            $action = ['href' => null, 'label' => null];
            $priorityLabel = null;

            switch ($notification->type) {
                case 'outbid':
                    $action = ['href' => isset($data['auction_id']) ? route('auctions.show', ['auction' => $data['auction_id']]) : route('bids.index'), 'label' => 'Vrati se na aukciju'];
                    $priorityLabel = 'Potrebna reakcija';
                    break;
                case 'auction_won':
                    $action = ['href' => isset($data['order_id']) ? route('orders.index') : route('bids.index'), 'label' => 'Završi kupovinu'];
                    $priorityLabel = 'Plaćanje';
                    break;
                case 'shipment':
                case 'item_shipped':
                    $action = ['href' => isset($data['order_id']) ? route('orders.index') : route('notifications.index', ['filter' => 'shipping']), 'label' => 'Prati isporuku'];
                    $priorityLabel = 'Isporuka';
                    break;
                case 'dispute_opened':
                case 'dispute_updated':
                case 'dispute_resolved':
                    $action = ['href' => isset($data['order_id']) ? route('orders.dispute.show', ['order' => $data['order_id']]) : route('orders.index'), 'label' => 'Otvori spor'];
                    $priorityLabel = 'Spor';
                    break;
                case 'saved_search_match':
                    $action = ['href' => isset($data['auction_id']) ? route('auctions.show', ['auction' => $data['auction_id']]) : route('searches.index'), 'label' => 'Pogledaj pogodak'];
                    $priorityLabel = 'Signal';
                    break;
                case 'message':
                    $action = ['href' => route('messages.index'), 'label' => 'Otvori poruke'];
                    $priorityLabel = 'Poruka';
                    break;
                default:
                    if (isset($data['auction_id'])) {
                        $action = ['href' => route('auctions.show', ['auction' => $data['auction_id']]), 'label' => 'Otvori aukciju'];
                    } elseif (isset($data['order_id'])) {
                        $action = ['href' => route('orders.index'), 'label' => 'Otvori narudžbe'];
                    }
                    break;
            }

            $notification->action_href = $action['href'];
            $notification->action_label = $action['label'];
            $notification->priority_label = $priorityLabel;

            return $notification;
        });

        $summaryCards = [
            [
                'label' => 'Traži reakciju',
                'value' => $notifications->whereNull('read_at')->whereIn('type', ['outbid', 'auction_won', 'dispute_opened', 'dispute_updated'])->count(),
                'href' => route('notifications.index', ['sort' => 'priority']),
            ],
            [
                'label' => 'Isporuke i plaćanja',
                'value' => $notifications->whereNull('read_at')->whereIn('type', ['shipment', 'item_shipped', 'auction_won'])->count(),
                'href' => route('notifications.index', ['filter' => 'shipping']),
            ],
            [
                'label' => 'Pretrage i signali',
                'value' => $notifications->whereNull('read_at')->whereIn('type', ['saved_search_match', 'auction_ending_soon'])->count(),
                'href' => route('notifications.index', ['filter' => 'searches']),
            ],
        ];

        return view('pages.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $notifications->whereNull('read_at')->count(),
            'filter' => $filter,
            'sort' => in_array($sort, ['priority', 'latest', 'oldest'], true) ? $sort : 'priority',
            'unreadByType' => $unreadByType,
            'summaryCards' => $summaryCards,
            'filters' => [
                ['value' => 'all', 'label' => 'Sve'],
                ['value' => 'messages', 'label' => 'Poruke'],
                ['value' => 'auctions', 'label' => 'Aukcije'],
                ['value' => 'shipping', 'label' => 'Isporuka'],
                ['value' => 'disputes', 'label' => 'Sporovi'],
                ['value' => 'searches', 'label' => 'Pretrage'],
            ],
            'sortOptions' => [
                ['value' => 'priority', 'label' => 'Po hitnosti'],
                ['value' => 'latest', 'label' => 'Najnovije'],
                ['value' => 'oldest', 'label' => 'Najstarije'],
            ],
        ]);
    })->name('notifications.index');

    Route::post('/obavijesti/procitano', function () {
        $user = auth()->user();
        abort_unless($user, 403);

        app(MarketplaceNotificationService::class)->markAllAsRead($user);

        return redirect()->route('notifications.index')->with('status', 'Sve obavijesti su označene kao pročitane.');
    })->name('notifications.read-all');

    Route::post('/obavijesti/procitano/filter', function () {
        $user = auth()->user();
        abort_unless($user, 403);

        $filter = request()->string('filter')->toString() ?: 'all';
        $query = $user->marketplaceNotifications()->whereNull('read_at');

        match ($filter) {
            'messages' => $query->where('type', 'message'),
            'auctions' => $query->whereIn('type', ['outbid', 'auction_won', 'auction_ending_soon']),
            'shipping' => $query->whereIn('type', ['shipment', 'item_shipped']),
            'disputes' => $query->whereIn('type', ['dispute_opened', 'dispute_updated', 'dispute_resolved']),
            'searches' => $query->where('type', 'saved_search_match'),
            default => $query,
        };

        $query->update(['read_at' => now()]);

        return redirect()->route('notifications.index', [
            'filter' => $filter === 'all' ? null : $filter,
            'sort' => request()->string('sort')->toString() ?: 'priority',
        ])
            ->with('status', 'Obavijesti za aktivni filter su označene kao pročitane.');
    })->name('notifications.read-filtered');

    Route::post('/obavijesti/{notification}/procitano', function (string $notification) {
        $user = auth()->user();
        abort_unless($user, 403);

        app(MarketplaceNotificationService::class)->markAsRead($user, $notification);

        return back()->with('status', 'Obavijest je označena kao pročitana.');
    })->name('notifications.read');

    // Wallet
    Route::get('/novcanik', function () {
        return view('pages.wallet.index');
    })->name('wallet.index');

    // Messages
    Route::get('/poruke', function () {
        $user = auth()->user();

        $threads = collect();
        $selectedThread = null;
        $threadMessages = collect();

        if ($user && Schema::hasTable('messages')) {
            $messages = Message::query()
                ->with(['sender', 'receiver', 'auction.primaryImage'])
                ->where(fn ($query) => $query
                    ->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id))
                ->latest('created_at')
                ->get();

            $threads = $messages
                ->groupBy(function (Message $message) use ($user): string {
                    $otherParticipantId = $message->sender_id === $user->id
                        ? ($message->receiver_id ?? 'guest')
                        : ($message->sender_id ?? 'guest');

                    return implode(':', [
                        $message->auction_id ?? 'general',
                        $otherParticipantId,
                    ]);
                })
                ->map(function ($conversation, string $threadKey) use ($user) {
                    /** @var Message $latest */
                    $latest = $conversation->first();
                    $otherParticipant = $latest->sender_id === $user->id ? $latest->receiver : $latest->sender;
                    $unreadCount = $conversation
                        ->where('receiver_id', $user->id)
                        ->where('is_read', false)
                        ->count();

                    return [
                        'thread_key' => $threadKey,
                        'other_user_id' => $otherParticipant?->id,
                        'contact' => $otherParticipant?->name ?? $otherParticipant?->email ?? 'Marketplace korisnik',
                        'auction_id' => $latest->auction?->id,
                        'auction_title' => $latest->auction?->title ?? 'Opća komunikacija',
                        'auction_image' => $latest->auction?->primaryImage?->url,
                        'last_message' => $latest->content,
                        'last_message_at' => $latest->created_at?->diffForHumans() ?? 'upravo sada',
                        'unread_count' => $unreadCount,
                        'message_count' => $conversation->count(),
                        'action_required' => $unreadCount > 0 && $latest->sender_id !== $user->id,
                    ];
                })
                ->values();

            $selectedThreadKey = request()->string('thread')->toString();
            $selectedThread = $threads->firstWhere('thread_key', $selectedThreadKey) ?? $threads->first();
            $systemThreadNote = null;

            if ($selectedThread) {
                $threadMessages = $messages
                    ->filter(function (Message $message) use ($selectedThread, $user): bool {
                        $otherParticipantId = $message->sender_id === $user->id ? $message->receiver_id : $message->sender_id;

                        return (string) ($message->auction_id ?? '') === (string) ($selectedThread['auction_id'] ?? '')
                            && (string) $otherParticipantId === (string) ($selectedThread['other_user_id'] ?? '');
                    })
                    ->sortBy('created_at')
                    ->values();

                if ($selectedThread['auction_id']) {
                    $systemThreadNote = "Sistemska poruka: komunikacija je povezana sa aukcijom '{$selectedThread['auction_title']}'. Sve izmjene statusa narudžbe i isporuke pratite kroz narudžbe i obavijesti.";
                }

                Message::query()
                    ->whereIn('id', $threadMessages->where('receiver_id', $user->id)->where('is_read', false)->pluck('id'))
                    ->update(['is_read' => true]);
            }
        }

        return view('pages.messages.index', [
            'threads' => $threads,
            'selectedThread' => $selectedThread,
            'systemThreadNote' => $systemThreadNote ?? null,
            'threadMessages' => $threadMessages,
        ]);
    })->name('messages.index');

    Route::post('/poruke', function () {
        $user = auth()->user();

        abort_unless($user && Schema::hasTable('messages'), 404);

        $validated = request()->validate([
            'receiver_id' => ['required', 'string'],
            'auction_id' => ['nullable', 'string'],
            'content' => ['required', 'string', 'max:5000'],
            'attachment_name' => ['nullable', 'string', 'max:255'],
            'attachment_url' => ['nullable', 'url', 'max:5000'],
            'attachment_file' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,pdf,webp'],
        ]);

        $attachmentName = $validated['attachment_name'] ?? null;
        $attachmentUrl = $validated['attachment_url'] ?? null;

        if (request()->hasFile('attachment_file')) {
            $file = request()->file('attachment_file');
            $directory = public_path('uploads/message-attachments');

            if (! File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            $storedName = (string) str()->uuid().'-'.preg_replace('/[^A-Za-z0-9.\-_]/', '-', $file->getClientOriginalName());
            $file->move($directory, $storedName);

            $attachmentName = $attachmentName ?: $file->getClientOriginalName();
            $attachmentUrl = asset('uploads/message-attachments/'.$storedName);
        }

        Message::query()->create([
            'sender_id' => $user->id,
            'receiver_id' => $validated['receiver_id'],
            'auction_id' => $validated['auction_id'] ?: null,
            'message_type' => 'user',
            'content' => $validated['content'],
            'attachment_name' => $attachmentName,
            'attachment_url' => $attachmentUrl,
            'is_read' => false,
        ]);

        $receiver = User::query()->find($validated['receiver_id']);

        if ($receiver) {
            app(MarketplaceNotificationService::class)->notify(
                $receiver,
                'message',
                'Nova poruka',
                'Primili ste novu poruku u razgovoru oko aukcije.',
                [
                    'auction_id' => $validated['auction_id'] ?: null,
                    'sender_id' => $user->id,
                ]
            );
        }

        $threadKey = implode(':', [
            $validated['auction_id'] ?: 'general',
            $validated['receiver_id'],
        ]);

        return redirect()->route('messages.index', ['thread' => $threadKey])
            ->with('status', 'Poruka je poslana.');
    })->name('messages.store');

    Route::get('/moje-pretrage', function () {
        $searches = auth()->user()
            ->savedSearches()
            ->latest()
            ->get();

        return view('pages.searches.index', ['searches' => $searches]);
    })->name('searches.index');

    Route::post('/moje-pretrage', function () {
        $user = auth()->user();

        $validated = request()->validate([
            'query' => ['required', 'string', 'max:255'],
            'category_slug' => ['nullable', 'string', 'max:255'],
        ]);

        SavedSearch::query()->firstOrCreate([
            'user_id' => $user->id,
            'query' => $validated['query'],
            'category_slug' => $validated['category_slug'] ?: null,
        ], [
            'alert_enabled' => true,
        ]);

        return redirect()->route('searches.index')->with('status', 'Pretraga je spremljena i alert je uključen.');
    })->name('searches.store');

    Route::delete('/moje-pretrage/{search}', function (SavedSearch $search) {
        abort_unless($search->user_id === auth()->id(), 403);
        $search->delete();

        return back()->with('status', 'Pretraga je uklonjena.');
    })->name('searches.destroy');

    // Orders
    Route::get('/narudzbe', function () {
        $orders = collect();

        if (Schema::hasTable('orders')) {
            $orders = auth()->user()
                ->orders()
                ->with(['auction.primaryImage', 'seller', 'shipment', 'dispute'])
                ->latest()
                ->get();
        }

        return view('pages.orders.index', ['orders' => $orders]);
    })->name('orders.index');

    Route::get('/narudzbe/{order}/spor', function (Order $order) {
        abort_unless($order->buyer_id === auth()->id(), 403);

        $relations = [
            'auction.primaryImage',
            'seller',
            'shipment',
        ];
        $disputeMessages = collect();

        if (Schema::hasTable('dispute_messages')) {
            $relations[] = 'dispute.messages.user';
        }

        $order->load($relations);

        abort_unless($order->dispute !== null, 404);

        if (Schema::hasTable('dispute_messages')) {
            $disputeMessages = $order->dispute->messages;
        }

        $disputeActions = collect([
            [
                'label' => 'Otvori poruke sa prodavačem',
                'hint' => 'Provjeri da li postoji nova poruka, tracking potvrda ili dogovor oko isporuke.',
                'href' => route('messages.index'),
            ],
            [
                'label' => 'Vrati se na narudžbu',
                'hint' => 'Pregledaj plaćanje, dostavu i povezanu aukciju na jednom mjestu.',
                'href' => route('orders.index'),
            ],
        ]);

        if ($order->shipment?->tracking_url) {
            $disputeActions->prepend([
                'label' => 'Prati pošiljku',
                'hint' => 'Kurirski tracking može zatvoriti spor bez dodatne eskalacije.',
                'href' => $order->shipment->tracking_url,
            ]);
        } elseif ($order->shipment?->tracking_number) {
            $disputeActions->prepend([
                'label' => 'Provjeri tracking broj',
                'hint' => 'Tracking broj je evidentiran i može pomoći pri rješavanju spora.',
                'href' => route('orders.index'),
            ]);
        }

        if (in_array((string) $order->dispute->status, ['open', 'in_review'], true)) {
            $disputeActions->push([
                'label' => 'Sačekaj moderatorsku odluku',
                'hint' => 'Spor je aktivan. Čuvaj komunikaciju i sve dokaze unutar platforme.',
                'href' => route('notifications.index', ['filter' => 'disputes']),
            ]);
        }

        return view('pages.orders.dispute-show', [
            'order' => $order,
            'dispute' => $order->dispute,
            'disputeMessages' => $disputeMessages,
            'disputeActions' => $disputeActions,
        ]);
    })->name('orders.dispute.show');
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    // Seller Dashboard
    Route::get('/dashboard', function () {
        $seller = auth()->user()->load('wallet');

        $stats = [
            ['label' => 'Aktivne aukcije', 'value' => Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'active')->count() : 0],
            ['label' => 'Ukupna prodaja', 'value' => Schema::hasTable('orders') ? number_format((float) $seller->soldOrders()->sum('seller_payout'), 2, ',', '.').' BAM' : '0,00 BAM'],
            ['label' => 'Wallet balans', 'value' => number_format((float) ($seller->wallet?->balance ?? 0), 2, ',', '.').' BAM'],
            ['label' => 'Prosječna ocjena', 'value' => number_format((float) $seller->trust_score, 1)],
        ];

        $activeAuctions = Schema::hasTable('auctions')
            ? $seller->auctions()->with(['category', 'primaryImage'])->where('status', 'active')->limit(4)->get()->map(fn (Auction $auction) => [
                'id' => $auction->id,
                'title' => $auction->title,
                'category' => $auction->category?->name ?? 'Bez kategorije',
                'price' => (float) $auction->current_price,
                'bids' => $auction->bids_count,
                'watchers' => $auction->watchers_count,
                'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                'time' => $auction->time_remaining,
                'image_url' => $auction->primaryImage?->url,
            ])
            : collect();

        $shippingQueue = Schema::hasTable('orders')
            ? $seller->soldOrders()->with('auction')->whereIn('status', ['awaiting_shipment', 'paid', 'shipped'])->latest()->limit(3)->get()->map(fn (Order $order) => [
                'id' => $order->id,
                'title' => $order->auction?->title ?? 'Aukcija',
                'status' => $order->status,
            ])
            : collect();

        $sellerCommandCenter = [
            ['label' => 'Nepročitane poruke', 'value' => Schema::hasTable('messages') ? Message::query()->where('receiver_id', $seller->id)->where('is_read', false)->count() : 0, 'href' => route('messages.index')],
            ['label' => 'Nove obavijesti', 'value' => Schema::hasTable('notifications_custom') ? $seller->marketplaceNotifications()->whereNull('read_at')->count() : 0, 'href' => route('notifications.index')],
            ['label' => 'Spremljene pretrage', 'value' => Schema::hasTable('saved_searches') ? $seller->savedSearches()->count() : 0, 'href' => route('searches.index')],
        ];

        $tierLimit = (int) ($seller->getTier()['auction_limit'] ?? 0);
        $activeAuctionCount = Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'active')->count() : 0;
        $storefrontHealth = $tierLimit > 0
            ? min(100, (int) round(($activeAuctionCount / $tierLimit) * 100))
            : 100;
        $featuredQuota = (int) ($seller->getTier()['featured_auctions'] ?? 0);
        $activeFeaturedCount = Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'active')->where('is_featured', true)->count() : 0;
        $draftCount = Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'draft')->count() : 0;

        $sellerPriorityQueue = [
            ['label' => 'Narudžbe za slanje', 'value' => $shippingQueue->count(), 'href' => route('seller.orders.index')],
            ['label' => 'Sporovi', 'value' => Schema::hasTable('disputes') ? $seller->soldOrders()->whereHas('dispute', fn ($query) => $query->whereIn('status', ['open', 'escalated']))->count() : 0, 'href' => route('admin.disputes.index')],
            ['label' => 'Outbid / aukcije', 'value' => Schema::hasTable('notifications_custom') ? $seller->marketplaceNotifications()->whereIn('type', ['outbid', 'auction_ending_soon'])->whereNull('read_at')->count() : 0, 'href' => route('notifications.index', ['filter' => 'auctions'])],
            ['label' => 'Storefront kapacitet', 'value' => $tierLimit > 0 ? "{$storefrontHealth}%" : 'Unlimited', 'href' => route('seller.auctions.index')],
            ['label' => 'Featured kvota', 'value' => $featuredQuota >= 0 ? "{$activeFeaturedCount}/{$featuredQuota}" : 'Unlimited', 'href' => route('seller.auctions.index')],
            ['label' => 'Draft spremnost', 'value' => $draftCount, 'href' => route('seller.auctions.index')],
            ['label' => 'Stari draftovi', 'value' => Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'draft')->where('created_at', '<=', now()->subDays(7))->count() : 0, 'href' => route('seller.auctions.index')],
            ['label' => 'Aukcije bez slika', 'value' => Schema::hasTable('auctions') ? $seller->auctions()->where('status', 'active')->whereDoesntHave('images')->count() : 0, 'href' => route('seller.auctions.index')],
        ];

        return view('pages.seller.dashboard', compact('stats', 'activeAuctions', 'shippingQueue', 'seller', 'sellerCommandCenter', 'sellerPriorityQueue'));
    })->name('dashboard');

    // Create Auction
    Route::get('/aukcije/nova', function () {
        return view('pages.seller.auctions.create');
    })->name('auctions.create');

    Route::get('/aukcije/{auction}/uredi', function () {
        return view('pages.seller.auctions.create');
    })->name('auctions.edit');

    // My Auctions
    Route::get('/aukcije', function () {
        $auctions = collect([
            ['id' => 1, 'title' => 'Tissot PRX Powermatic', 'category' => 'Satovi', 'price' => 1220.00, 'bids' => 7, 'watchers' => 18, 'location' => 'Tuzla', 'time' => '2d 05h', 'image_url' => null, 'status' => 'active'],
            ['id' => 2, 'title' => 'Fender Telecaster Player', 'category' => 'Muzička oprema', 'price' => 1780.00, 'bids' => 13, 'watchers' => 21, 'location' => 'Sarajevo', 'time' => '1d 04h', 'image_url' => null, 'status' => 'active'],
        ]);
        $readinessItems = collect();

        if (Schema::hasTable('auctions')) {
            $databaseAuctions = Auction::query()
                ->with(['category', 'primaryImage'])
                ->where('seller_id', auth()->id())
                ->latest()
                ->get()
                ->map(fn (Auction $auction) => [
                    'id' => $auction->id,
                    'title' => $auction->title,
                    'category' => $auction->category?->name ?? 'Bez kategorije',
                    'price' => (float) $auction->current_price,
                    'bids' => $auction->bids_count,
                    'watchers' => $auction->watchers_count,
                    'location' => $auction->location_city ?? $auction->location ?? 'Nepoznato',
                    'time' => $auction->time_remaining,
                    'image_url' => $auction->primaryImage?->url,
                    'status' => (string) $auction->status,
                ]);

            if ($databaseAuctions->isNotEmpty()) {
                $auctions = $databaseAuctions;
            }

            $sellerAuctions = Auction::query()
                ->where('seller_id', auth()->id())
                ->with('images')
                ->latest()
                ->get();

            $readinessItems = $sellerAuctions
                ->flatMap(function (Auction $auction): array {
                    $items = [];

                    if ($auction->status === 'draft' && $auction->created_at !== null && $auction->created_at->lte(now()->subDays(7))) {
                        $items[] = [
                            'title' => $auction->title,
                            'hint' => 'Draft je stariji od 7 dana. Dovrši listing i objavi ga.',
                            'href' => route('seller.auctions.edit', ['auction' => $auction->id]),
                            'cta' => 'Dovrši draft',
                        ];
                    }

                    if ($auction->status === 'active' && $auction->images->isEmpty()) {
                        $items[] = [
                            'title' => $auction->title,
                            'hint' => 'Aktivna aukcija nema nijednu sliku. Dodaj featured fotografiju.',
                            'href' => route('seller.auctions.edit', ['auction' => $auction->id]),
                            'cta' => 'Dodaj slike',
                        ];
                    }

                    if (blank($auction->description) || mb_strlen((string) $auction->description) < 40) {
                        $items[] = [
                            'title' => $auction->title,
                            'hint' => 'Opis je prekratak za kvalitetan listing i SEO.',
                            'href' => route('seller.auctions.edit', ['auction' => $auction->id]),
                            'cta' => 'Dopuni opis',
                        ];
                    }

                    if (blank($auction->shipping_info)) {
                        $items[] = [
                            'title' => $auction->title,
                            'hint' => 'Nedostaju podaci o dostavi ili preuzimanju.',
                            'href' => route('seller.auctions.edit', ['auction' => $auction->id]),
                            'cta' => 'Dodaj dostavu',
                        ];
                    }

                    return $items;
                })
                ->unique(fn (array $item) => $item['title'].'|'.$item['cta'])
                ->take(6)
                ->values();
        }

        return view('pages.seller.auctions.index', [
            'auctions' => $auctions,
            'readinessItems' => $readinessItems,
        ]);
    })->name('auctions.index');

    // Orders
    Route::get('/narudzbe/export', function () {
        $orders = Schema::hasTable('orders')
            ? Order::query()->where('seller_id', auth()->id())->latest()->get()
            : collect();

        return response()->streamDownload(function () use ($orders) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['order_id', 'status', 'amount', 'buyer_id']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->id,
                    $order->status,
                    $order->total_amount ?? $order->amount,
                    $order->buyer_id,
                ]);
            }

            fclose($handle);
        }, 'seller-orders.csv', ['Content-Type' => 'text/csv']);
    })->name('orders.export');

    Route::get('/narudzbe', function () {
        $orders = collect([
            ['id' => 881, 'title' => 'Canon R6', 'buyer' => 'Jasmin K.', 'amount' => '2.140,00 BAM', 'status' => 'awaiting_shipment'],
            ['id' => 879, 'title' => 'Gramofon Technics', 'buyer' => 'Lana R.', 'amount' => '980,00 BAM', 'status' => 'pending_payment'],
        ]);
        $operationsInbox = collect();

        if (Schema::hasTable('orders')) {
            $databaseOrders = Order::query()
                ->with(['buyer', 'auction', 'shipment', 'dispute'])
                ->where('seller_id', auth()->id())
                ->latest()
                ->get()
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'title' => $order->auction?->title ?? 'Aukcija',
                    'buyer' => $order->buyer?->name ?? 'Kupac',
                    'amount' => number_format((float) ($order->total_amount ?? $order->amount ?? 0), 2, ',', '.').' BAM',
                    'status' => $order->status,
                    'payment_deadline_at' => $order->payment_deadline_at?->format('d.m.Y. H:i'),
                    'tracking_number' => $order->shipment?->tracking_number,
                    'dispute_status' => $order->dispute?->status,
                ]);

            if ($databaseOrders->isNotEmpty()) {
                $orders = $databaseOrders;
            }

            $sellerOrders = Order::query()
                ->with(['buyer', 'auction', 'shipment', 'dispute'])
                ->where('seller_id', auth()->id())
                ->latest()
                ->get();

            $operationsInbox = collect([
                [
                    'label' => 'Čeka uplatu',
                    'value' => $sellerOrders->where('status', 'pending_payment')->count(),
                    'hint' => 'Narudžbe koje još ne smiju u otpremu dok kupac ne uplati.',
                    'href' => route('seller.orders.index'),
                ],
                [
                    'label' => 'Spremno za slanje',
                    'value' => $sellerOrders->whereIn('status', ['paid', 'awaiting_shipment'])->count(),
                    'hint' => 'Narudžbe koje trebaju kurira i tracking broj.',
                    'href' => route('seller.orders.index'),
                ],
                [
                    'label' => 'Otvoreni sporovi',
                    'value' => $sellerOrders->filter(fn (Order $order) => in_array((string) $order->dispute?->status, ['open', 'escalated', 'in_review'], true))->count(),
                    'hint' => 'Slučajevi koji traže odgovor ili dodatne dokaze.',
                    'href' => route('seller.orders.index'),
                ],
            ]);
        }

        return view('pages.seller.orders.index', [
            'orders' => $orders,
            'operationsInbox' => $operationsInbox,
        ]);
    })->name('orders.index');

    Route::get('/narudzbe/{order}', function () {
        $orderRecord = Schema::hasTable('orders')
            ? Order::query()->with(['buyer', 'auction', 'shipment', 'dispute'])->find(request()->route('order'))
            : null;

        abort_if($orderRecord && $orderRecord->seller_id !== auth()->id(), 403);

        $order = (object) [
            'id' => $orderRecord?->id ?? request()->route('order') ?? 881,
            'title' => $orderRecord?->auction?->title ?? 'Canon R6 Body',
            'status' => $orderRecord?->status ?? 'awaiting_shipment',
            'buyer_name' => $orderRecord?->buyer?->name ?? 'Jasmin K.',
            'buyer_email' => $orderRecord?->buyer?->email ?? 'jasmin@example.test',
            'buyer_phone' => $orderRecord?->buyer?->phone ?? '+387 61 111 222',
            'shipping_address' => is_array($orderRecord?->shipping_address) ? implode(', ', $orderRecord->shipping_address) : ($orderRecord?->shipping_address ?: 'Zmaja od Bosne 14, Sarajevo'),
            'shipping_note' => $orderRecord?->shipping_method ?? 'Brza pošta, isporuka 1-2 dana',
            'amount' => number_format((float) ($orderRecord?->total_amount ?? $orderRecord?->amount ?? 2140), 2, ',', '.').' BAM',
            'commission' => number_format((float) ($orderRecord?->commission_amount ?? $orderRecord?->commission ?? 107), 2, ',', '.').' BAM',
            'payout' => number_format((float) ($orderRecord?->seller_payout ?? 2033), 2, ',', '.').' BAM',
            'tracking_number' => $orderRecord?->shipment?->tracking_number ?? 'EE123456789BA',
            'payment_deadline_at' => $orderRecord?->payment_deadline_at?->format('d.m.Y. H:i'),
            'dispute_status' => $orderRecord?->dispute?->status,
            'dispute_reason' => $orderRecord?->dispute?->reason,
        ];

        $orderActions = collect([
            [
                'label' => 'Otvori narudžbe',
                'hint' => 'Vrati se na seller inbox svih narudžbi.',
                'href' => route('seller.orders.index'),
            ],
            [
                'label' => 'Pregledaj poruke',
                'hint' => 'Provjeri da li je buyer ostavio dodatne instrukcije ili pitanje.',
                'href' => route('messages.index'),
            ],
        ]);

        if ($orderRecord?->dispute) {
            $orderActions->prepend([
                'label' => 'Otvori seller spor',
                'hint' => 'Pregledaj razlog spora i operativne korake za odgovor.',
                'href' => route('seller.orders.dispute.show', ['order' => $order->id]),
            ]);
        }

        return view('pages.seller.orders.show', [
            'order' => $order,
            'orderActions' => $orderActions,
        ]);
    })->name('orders.show');

    Route::get('/narudzbe/{order}/spor', function (Order $order) {
        abort_unless($order->seller_id === auth()->id(), 403);

        $order->load([
            'auction.primaryImage',
            'buyer',
            'shipment',
            'dispute',
        ]);

        abort_unless($order->dispute !== null, 404);

        $sellerDisputeActions = collect([
            [
                'label' => 'Otvori poruke sa kupcem',
                'hint' => 'Provjeri postoji li nova poruka, dokaz ili zahtjev za povrat.',
                'href' => route('messages.index'),
            ],
            [
                'label' => 'Nazad na detalj narudžbe',
                'hint' => 'Vrati se na finansije, shipment i fulfillment akcije.',
                'href' => route('seller.orders.show', ['order' => $order->id]),
            ],
        ]);

        if ($order->shipment?->tracking_number) {
            $sellerDisputeActions->prepend([
                'label' => 'Potvrdi tracking kupcu',
                'hint' => 'Tracking broj često rješava spor bez dodatne eskalacije.',
                'href' => route('seller.orders.show', ['order' => $order->id]),
            ]);
        }

        return view('pages.seller.orders.dispute-show', [
            'order' => $order,
            'dispute' => $order->dispute,
            'sellerDisputeActions' => $sellerDisputeActions,
        ]);
    })->name('orders.dispute.show');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Feature Flags Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'role:super_admin|moderator'])->group(function () {
    Route::get('/feature-flags', [FeatureFlagController::class, 'index'])->name('admin.feature-flags.index');
    Route::post('/feature-flags', [FeatureFlagController::class, 'store'])->name('admin.feature-flags.store');
    Route::patch('/feature-flags/{flag}/toggle', [FeatureFlagController::class, 'toggle'])->name('admin.feature-flags.toggle');
    Route::delete('/feature-flags/{flag}', [FeatureFlagController::class, 'destroy'])->name('admin.feature-flags.destroy');
});

Route::middleware(['auth', 'role:admin|moderator'])->prefix('admin')->name('admin.')->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', function () {
        $stats = [
            ['label' => 'Ukupno korisnika', 'value' => Schema::hasTable('users') ? number_format((int) DB::table('users')->count(), 0, ',', '.') : '0'],
            ['label' => 'Aktivne aukcije', 'value' => Schema::hasTable('auctions') ? number_format((int) DB::table('auctions')->where('status', 'active')->count(), 0, ',', '.') : '0'],
            ['label' => 'Današnjih bidova', 'value' => Schema::hasTable('bids') ? number_format((int) DB::table('bids')->whereDate('created_at', today())->count(), 0, ',', '.') : '0'],
            ['label' => 'Prihod ovog mjeseca', 'value' => Schema::hasTable('orders') ? number_format((float) DB::table('orders')->whereMonth('created_at', now()->month)->sum('commission_amount'), 2, ',', '.').' BAM' : '0,00 BAM'],
        ];

        $priorities = collect([
            ['variant' => 'warning', 'text' => 'KYC queue zahtijeva pregled.'],
            ['variant' => 'danger', 'text' => 'Otvoreni sporovi čekaju moderatorsku odluku.'],
            ['variant' => 'info', 'text' => 'Flagovane aukcije traže ručnu provjeru sadržaja.'],
        ]);

        if (Schema::hasTable('user_verifications') || Schema::hasTable('disputes') || Schema::hasTable('auctions')) {
            $priorities = collect([
                ['variant' => 'warning', 'text' => (Schema::hasTable('user_verifications') ? DB::table('user_verifications')->where('status', 'pending')->count() : 0).' KYC zahtjeva čeka pregled.'],
                ['variant' => 'danger', 'text' => (Schema::hasTable('disputes') ? DB::table('disputes')->where('status', 'open')->count() : 0).' sporova zahtijeva odluku moderatora.'],
                ['variant' => 'info', 'text' => (Schema::hasTable('auctions') ? DB::table('auctions')->whereIn('status', ['reported', 'pending_review'])->count() : 0).' aukcija je označeno za ručnu provjeru.'],
            ]);
        }

        $activity = collect([
            'Moderator odobrio KYC za seller račun.',
            'Aukcija je označena kao featured.',
            'Otvoren novi dispute za narudžbu.',
        ]);

        if (Schema::hasTable('admin_logs')) {
            $databaseActivity = DB::table('admin_logs')
                ->latest('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($log) => strtoupper((string) $log->action).' · '.($log->target_type ?? 'sistem').' · '.substr((string) $log->target_id, 0, 8));

            if ($databaseActivity->isNotEmpty()) {
                $activity = $databaseActivity;
            }
        }

        return view('pages.admin.dashboard', compact('stats', 'priorities', 'activity'));
    })->name('dashboard');

    Route::get('/aktivnost', function () {
        $entries = collect();

        if (Schema::hasTable('admin_logs')) {
            $entries = AdminLog::query()
                ->with('admin')
                ->latest('created_at')
                ->limit(100)
                ->get();
        }

        return view('pages.admin.activity.index', ['entries' => $entries]);
    })->name('activity.index');

    // Users
    Route::get('/korisnici', function () {
        return view('pages.admin.users.index');
    })->name('users.index');

    Route::get('/korisnici/{user}', function () {
        return view('pages.admin.users.show');
    })->name('users.show');

    Route::get('/sadrzaj/stranice', function () {
        $pages = collect([
            ['id' => '1', 'title' => 'O nama', 'slug' => 'o-nama', 'page_type' => 'company', 'is_published' => true],
            ['id' => '2', 'title' => 'Uvjeti korištenja', 'slug' => 'uvjeti-koristenja', 'page_type' => 'legal', 'is_published' => true],
            ['id' => '3', 'title' => 'Politika privatnosti', 'slug' => 'politika-privatnosti', 'page_type' => 'legal', 'is_published' => true],
        ]);

        if (Schema::hasTable('content_pages')) {
            $databasePages = ContentPage::query()->latest('updated_at')->get();

            if ($databasePages->isNotEmpty()) {
                $pages = $databasePages;
            }
        }

        return view('pages.admin.content.pages.index', ['pages' => $pages]);
    })->name('content.pages.index');

    Route::get('/sadrzaj/stranice/uredi/{slug?}', function () {
        $slug = request()->route('slug');
        $page = null;

        if ($slug && Schema::hasTable('content_pages')) {
            $page = ContentPage::query()->where('slug', $slug)->first();
        }

        $page ??= (object) [
            'title' => '',
            'slug' => $slug ?? '',
            'page_type' => 'legal',
            'excerpt' => '',
            'body' => '',
            'is_published' => true,
        ];

        return view('pages.admin.content.pages.edit', ['page' => $page]);
    })->name('content.pages.edit');

    Route::post('/sadrzaj/stranice', function () {
        abort_unless(Schema::hasTable('content_pages'), 404);

        $validated = request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'page_type' => ['required', 'string', 'max:50'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        ContentPage::query()->updateOrCreate(
            ['slug' => $validated['slug']],
            [
                'title' => $validated['title'],
                'page_type' => $validated['page_type'],
                'excerpt' => $validated['excerpt'] ?? null,
                'body' => $validated['body'],
                'is_published' => (bool) ($validated['is_published'] ?? false),
                'published_at' => ($validated['is_published'] ?? false) ? now() : null,
            ]
        );

        return redirect()->route('admin.content.pages.index')->with('status', 'Stranica je sačuvana.');
    })->name('content.pages.store');

    Route::get('/sadrzaj/vijesti', function () {
        $articles = collect([
            ['id' => '1', 'title' => 'Sigurnosni savjeti za kupce i prodavače', 'slug' => 'sigurnosni-savjeti-za-kupce', 'is_published' => true],
        ]);

        if (Schema::hasTable('news_articles')) {
            $databaseArticles = NewsArticle::query()->latest('updated_at')->get();

            if ($databaseArticles->isNotEmpty()) {
                $articles = $databaseArticles;
            }
        }

        return view('pages.admin.content.news.index', ['articles' => $articles]);
    })->name('content.news.index');

    Route::get('/sadrzaj/vijesti/uredi/{slug?}', function () {
        $slug = request()->route('slug');
        $article = null;

        if ($slug && Schema::hasTable('news_articles')) {
            $article = NewsArticle::query()->where('slug', $slug)->first();
        }

        $article ??= (object) [
            'title' => '',
            'slug' => $slug ?? '',
            'excerpt' => '',
            'body' => '',
            'is_published' => true,
        ];

        return view('pages.admin.content.news.edit', ['article' => $article]);
    })->name('content.news.edit');

    Route::post('/sadrzaj/vijesti', function () {
        abort_unless(Schema::hasTable('news_articles'), 404);

        $validated = request()->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        NewsArticle::query()->updateOrCreate(
            ['slug' => $validated['slug']],
            [
                'title' => $validated['title'],
                'excerpt' => $validated['excerpt'] ?? null,
                'body' => $validated['body'],
                'is_published' => (bool) ($validated['is_published'] ?? false),
                'published_at' => ($validated['is_published'] ?? false) ? now() : null,
            ]
        );

        return redirect()->route('admin.content.news.index')->with('status', 'Vijest je sačuvana.');
    })->name('content.news.store');

    // Auctions
    Route::get('/aukcije', function () {
        return view('pages.admin.auctions.index');
    })->name('auctions.index');

    Route::get('/aukcije/{auction}', function () {
        return view('pages.admin.auctions.show');
    })->name('auctions.show');

    // Categories
    Route::get('/kategorije', function () {
        return view('pages.admin.categories.index');
    })->name('categories.index');

    // Disputes
    Route::get('/sporovi', function () {
        return view('pages.admin.disputes.index');
    })->name('disputes.index');

    Route::get('/sporovi/{dispute}', function () {
        return view('pages.admin.disputes.show');
    })->name('disputes.show');

    // Statistics
    Route::get('/statistike', function () {
        return view('pages.admin.statistics');
    })->name('statistics');
});
