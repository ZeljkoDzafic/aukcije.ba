<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * T-1502: Category merchandizing — sort order and featured placement.
 */
class CategoryMerchandizingService
{
    /**
     * Reorder categories by providing an ordered list of IDs.
     *
     * @param string[] $orderedIds Category IDs in desired display order.
     */
    public function reorder(array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $sortOrder => $id) {
                Category::query()
                    ->where('id', $id)
                    ->update(['sort_order' => $sortOrder + 1]);
            }
        });

        Cache::forget('categories:tree');
    }

    /**
     * Set featured flag on a category (for homepage placement).
     */
    public function setFeatured(string $categoryId, bool $featured): void
    {
        Category::query()
            ->where('id', $categoryId)
            ->update(['is_featured' => $featured]);

        Cache::forget('categories:tree');
        Cache::forget('homepage:featured');
    }

    /**
     * Toggle active status for a category.
     */
    public function setActive(string $categoryId, bool $active): void
    {
        Category::query()
            ->where('id', $categoryId)
            ->update(['is_active' => $active]);

        Cache::forget('categories:tree');
    }
}
