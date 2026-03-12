<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(): JsonResponse
    {
        $disputes = Dispute::query()->with(['order', 'openedBy'])->latest()->get();

        return response()->json(['success' => true, 'data' => $disputes]);
    }

    public function show(Dispute $dispute): JsonResponse
    {
        $dispute->load(['order', 'messages', 'openedBy', 'resolvedBy']);

        return response()->json(['success' => true, 'data' => $dispute]);
    }

    public function resolve(Request $request, Dispute $dispute): JsonResponse
    {
        $validated = $request->validate([
            'resolution' => ['required', 'string', 'max:255'],
        ]);

        $payload = [
            'status' => 'resolved',
            'resolution' => $validated['resolution'],
        ];

        if (\Illuminate\Support\Facades\Schema::hasColumn('disputes', 'resolved_by')) {
            $payload['resolved_by'] = $request->user()->id;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('disputes', 'resolved_by_id')) {
            $payload['resolved_by_id'] = $request->user()->id;
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('disputes', 'resolved_at')) {
            $payload['resolved_at'] = now();
        }

        $dispute->update($payload);

        return response()->json(['success' => true, 'data' => $dispute->fresh()]);
    }
}
