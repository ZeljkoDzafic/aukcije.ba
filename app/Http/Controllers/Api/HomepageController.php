<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HomepageDataService;
use Illuminate\Http\JsonResponse;

/**
 * T-1202: Homepage sections API — 4 curated auction feeds.
 */
class HomepageController extends Controller
{
    public function __construct(private readonly HomepageDataService $homepageService) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->homepageService->all()]);
    }
}
