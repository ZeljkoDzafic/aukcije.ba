<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class CategoryManager extends Component
{
    public string $selectedCategory = 'Elektronika';

    public string $name = 'Elektronika';

    public string $slug = 'elektronika';

    public string $icon = 'device-mobile';

    public string $parent = '';

    public string $statusMessage = '';

    /** @var list<array{name: string, count: int, status: string, children: list<string>}> */
    public array $categories = [
        [
            'name' => 'Elektronika',
            'count' => 412,
            'status' => 'Aktivna',
            'children' => ['Mobiteli · 188 aukcija', 'Laptopi · 96 aukcija', 'Audio · 128 aukcija'],
        ],
        [
            'name' => 'Satovi',
            'count' => 203,
            'status' => 'Aktivna',
            'children' => ['Luksuzni satovi · 64 aukcije', 'Vintage satovi · 91 aukcija'],
        ],
    ];

    public function selectCategory(string $name): void
    {
        $this->selectedCategory = $name;
        $this->name = $name;
        $this->slug = str($name)->slug()->value();
    }

    public function saveCategory(): void
    {
        if (Schema::hasTable('categories')) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $this->slug],
                [
                    'name' => $this->name,
                    'icon' => $this->icon,
                    'parent_id' => $this->parent ?: null,
                    'is_active' => true,
                ]
            );

            $this->statusMessage = "Kategorija '{$category->name}' je sačuvana.";

            return;
        }

        $this->statusMessage = "Demo kategorija '{$this->name}' je ažurirana.";
    }

    public function toggleSelectedCategory(): void
    {
        if (Schema::hasTable('categories')) {
            $category = Category::query()->where('slug', $this->slug)->first();

            if ($category) {
                $category->update(['is_active' => ! $category->is_active]);
                $this->statusMessage = "Kategorija '{$category->name}' je ".($category->is_active ? 'deaktivirana' : 'aktivirana').'.';
            }

            return;
        }

        $this->statusMessage = "Demo status za '{$this->name}' je promijenjen.";
    }

    public function moveCategory(string $name, string $direction): void
    {
        if (! Schema::hasTable('categories')) {
            $this->statusMessage = "Demo reorder za '{$name}' je evidentiran.";

            return;
        }

        $category = Category::query()->where('name', $name)->first();

        if (! $category) {
            return;
        }

        $currentOrder = (int) ($category->sort_order ?? 0);
        $targetOrder = $direction === 'up' ? max(0, $currentOrder - 1) : $currentOrder + 1;

        $swap = Category::query()->where('sort_order', $targetOrder)->first();

        if ($swap) {
            $swap->update(['sort_order' => $currentOrder]);
        }

        $category->update(['sort_order' => $targetOrder]);
        $this->statusMessage = "Redoslijed kategorije '{$category->name}' je ažuriran.";
    }

    public function render(): View
    {
        if (Schema::hasTable('categories')) {
            $databaseCategories = Category::query()
                ->withCount('auctions')
                ->whereNull('parent_id')
                ->limit(10)
                ->get()
                ->map(function (Category $category): array {
                    return [
                        'name' => $category->name,
                        'count' => (int) $category->auctions_count,
                        'status' => $category->is_active ? 'Aktivna' : 'Neaktivna',
                        'children' => $category->children()->limit(5)->get()->map(fn (Category $child): string => "{$child->name} · {$child->auctions()->count()} aukcija")->values()->all(),
                    ];
                });

            if ($databaseCategories->isNotEmpty()) {
                $this->categories = $databaseCategories->all();
            }
        }

        return view('livewire.admin.category-manager');
    }
}
