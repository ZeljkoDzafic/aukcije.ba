<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class RatingController extends Controller
{
    public function rate(string $order): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['order_id' => $order]]);
    }
}
