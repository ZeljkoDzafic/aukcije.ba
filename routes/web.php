<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\FeatureFlagController;
use App\Models\Auction;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Web Routes - Public Pages
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('pages.home');
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
    return view('pages.categories.index');
})->name('categories.index');

Route::get('/kategorije/{category}', function () {
    $slug = (string) request()->route('category');

    $categoryRecord = Schema::hasTable('categories')
        ? Category::query()->where('slug', $slug)->withCount('auctions')->first()
        : null;

    $category = (object) [
        'name' => $categoryRecord?->name ?? str($slug)->replace('-', ' ')->title()->value(),
        'slug' => $categoryRecord?->slug ?? $slug,
        'description' => 'Pregled tržišta, najtraženijih artikala i aukcija koje završavaju uskoro.',
        'auctions_count' => $categoryRecord?->auctions_count ?? 3,
    ];

    $auctions = collect([
        ['id' => 1, 'title' => 'Samsung Galaxy S24 Ultra', 'category' => $category->name, 'price' => 1250.00, 'bids' => 14, 'watchers' => 32, 'location' => 'Sarajevo', 'time' => '2d 04h'],
        ['id' => 2, 'title' => 'Sony WH-1000XM5', 'category' => $category->name, 'price' => 480.00, 'bids' => 11, 'watchers' => 17, 'location' => 'Banja Luka', 'time' => '5h 12m'],
        ['id' => 3, 'title' => 'Nintendo Switch OLED', 'category' => $category->name, 'price' => 410.00, 'bids' => 8, 'watchers' => 29, 'location' => 'Zenica', 'time' => '9h 48m'],
    ]);

    if ($categoryRecord && Schema::hasTable('auctions')) {
        $databaseAuctions = Auction::query()
            ->with('category')
            ->where('category_id', $categoryRecord->id)
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
            ]);

        if ($databaseAuctions->isNotEmpty()) {
            $auctions = $databaseAuctions;
        }
    }

    return view('pages.categories.show', compact('category', 'auctions'));
})->name('categories.show');

// Search
Route::get('/pretraga', function () {
    return view('pages.search');
})->name('search');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

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
    Route::get('/verify-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Watchlist
    Route::get('/watchlist', function () {
        return view('pages.watchlist.index');
    })->name('watchlist.index');

    // Profile
    Route::get('/profil', function () {
        return view('pages.profile.index');
    })->name('profile.index');

    // Wallet
    Route::get('/novcanik', function () {
        return view('pages.wallet.index');
    })->name('wallet.index');

    // Messages
    Route::get('/poruke', function () {
        return view('pages.messages.index');
    })->name('messages.index');

    // Orders
    Route::get('/narudzbe', function () {
        return view('pages.orders.index');
    })->name('orders.index');
});

/*
|--------------------------------------------------------------------------
| Seller Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'seller'])->prefix('seller')->name('seller.')->group(function () {
    // Seller Dashboard
    Route::get('/dashboard', function () {
        return view('pages.seller.dashboard');
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
        return view('pages.seller.auctions.index');
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

        if (Schema::hasTable('orders')) {
            $databaseOrders = Order::query()
                ->with(['buyer', 'auction'])
                ->where('seller_id', auth()->id())
                ->latest()
                ->get()
                ->map(fn (Order $order) => [
                    'id' => $order->id,
                    'title' => $order->auction?->title ?? 'Aukcija',
                    'buyer' => $order->buyer?->name ?? 'Kupac',
                    'amount' => number_format((float) ($order->total_amount ?? $order->amount ?? 0), 2, ',', '.').' BAM',
                    'status' => $order->status,
                ]);

            if ($databaseOrders->isNotEmpty()) {
                $orders = $databaseOrders;
            }
        }

        return view('pages.seller.orders.index', ['orders' => $orders]);
    })->name('orders.index');

    Route::get('/narudzbe/{order}', function () {
        $orderRecord = Schema::hasTable('orders')
            ? Order::query()->with(['buyer', 'auction', 'shipment'])->find(request()->route('order'))
            : null;

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
        ];

        return view('pages.seller.orders.show', ['order' => $order]);
    })->name('orders.show');
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
        return view('pages.admin.dashboard');
    })->name('dashboard');

    // Users
    Route::get('/korisnici', function () {
        return view('pages.admin.users.index');
    })->name('users.index');

    Route::get('/korisnici/{user}', function () {
        return view('pages.admin.users.show');
    })->name('users.show');

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
