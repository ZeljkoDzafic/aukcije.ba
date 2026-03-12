<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function profile(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => null]);
    }

    public function update(): JsonResponse
    {
        return response()->json(['success' => true, 'message' => 'Profile placeholder']);
    }
}
