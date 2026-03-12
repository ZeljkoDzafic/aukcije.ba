<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Auction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuctionActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $auction = $request->route('auction');

        if ($auction instanceof Auction && $auction->status !== 'active') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Aukcija nije aktivna.'], 422);
            }

            return redirect()->back()->with('error', 'Aukcija nije aktivna.');
        }

        return $next($request);
    }
}
