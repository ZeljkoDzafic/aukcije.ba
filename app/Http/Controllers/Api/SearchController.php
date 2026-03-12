<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q'         => ['required', 'string', 'min:2', 'max:200'],
            'category'  => ['nullable', 'string'],
            'price_min' => ['nullable', 'numeric', 'min:0'],
            'price_max' => ['nullable', 'numeric', 'min:0'],
            'condition' => ['nullable', 'string', 'in:new,used,refurbished'],
            'location'  => ['nullable', 'string', 'max:100'],
            'sort'      => ['nullable', 'string', 'in:relevance,ending_soon,price_low,price_high,newest'],
            'page'      => ['nullable', 'integer', 'min:1'],
        ]);

        $q = $request->string('q')->toString();

        if (config('scout.driver') !== 'null') {
            $builder = Auction::search($q)
                ->query(function ($query) use ($request) {
                    $query->active()
                        ->with(['category', 'primaryImage'])
                        ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category'))))
                        ->when($request->filled('price_min'), fn ($q) => $q->where('current_price', '>=', $request->float('price_min')))
                        ->when($request->filled('price_max'), fn ($q) => $q->where('current_price', '<=', $request->float('price_max')))
                        ->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->string('condition')));
                });

            match ($request->input('sort', 'relevance')) {
                'ending_soon' => $builder->orderBy('ends_at', 'asc'),
                'price_low'   => $builder->orderBy('current_price', 'asc'),
                'price_high'  => $builder->orderBy('current_price', 'desc'),
                'newest'      => $builder->orderBy('created_at', 'desc'),
                default       => null,
            };

            $results = $builder->paginate(20);
        } else {
            $query = Auction::query()
                ->active()
                ->with(['category', 'primaryImage'])
                ->search($q)
                ->when($request->filled('category'), fn ($q) => $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category'))))
                ->when($request->filled('price_min'), fn ($q) => $q->where('current_price', '>=', $request->float('price_min')))
                ->when($request->filled('price_max'), fn ($q) => $q->where('current_price', '<=', $request->float('price_max')))
                ->when($request->filled('condition'), fn ($q) => $q->where('condition', $request->string('condition')));

            match ($request->input('sort', 'relevance')) {
                'ending_soon' => $query->orderBy('ends_at'),
                'price_low'   => $query->orderBy('current_price'),
                'price_high'  => $query->orderByDesc('current_price'),
                'newest'      => $query->latest(),
                default       => $query->orderBy('ends_at'),
            };

            $results = $query->paginate(20);
        }

        return response()->json([
            'success' => true,
            'data' => $results->items(),
            'meta' => [
                'current_page' => $results->currentPage(),
                'last_page'    => $results->lastPage(),
                'per_page'     => $results->perPage(),
                'total'        => $results->total(),
            ],
        ]);
    }
}
