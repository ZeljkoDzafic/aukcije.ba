<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }
}
