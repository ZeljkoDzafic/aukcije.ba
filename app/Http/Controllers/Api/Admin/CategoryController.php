<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::query()->withCount('auctions')->orderBy('sort_order')->get();

        return response()->json(['success' => true, 'data' => $categories]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'icon' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category = Category::query()->create($validated + ['is_active' => $validated['is_active'] ?? true]);

        return response()->json(['success' => true, 'data' => $category], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category->load(['children', 'parent']);

        return response()->json(['success' => true, 'data' => $category]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
            'icon' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update($validated);

        return response()->json(['success' => true, 'data' => $category->fresh()]);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json(['success' => true, 'data' => ['id' => $category->id]]);
    }
}
