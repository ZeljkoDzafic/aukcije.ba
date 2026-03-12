<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function show(string $order): JsonResponse
    {
        return response()->json(['success' => true, 'data' => ['id' => $order]]);
    }
}
