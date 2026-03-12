<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function search(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }
}
