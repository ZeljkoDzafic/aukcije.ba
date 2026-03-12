<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class MessageController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => []]);
    }

    public function store(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Message placeholder']);
    }
}
