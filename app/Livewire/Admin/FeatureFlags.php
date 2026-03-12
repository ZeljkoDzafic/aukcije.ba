<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\FeatureFlag;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class FeatureFlags extends Component
{
    public string $name = '';

    public string $description = '';

    public bool $isActive = false;

    public string $feedback = '';

    /** @var list<array{id: int, name: string, description: string, is_active: bool, group: string}> */
    public array $fallbackFlags = [
        ['id' => 1, 'name' => 'proxy_bidding', 'description' => 'Proxy bidding UI i workflow', 'is_active' => true, 'group' => 'Bidding'],
        ['id' => 2, 'name' => 'wallet_topups', 'description' => 'Dopuna walleta putem gatewaya', 'is_active' => true, 'group' => 'Payments'],
        ['id' => 3, 'name' => 'courier_tracking', 'description' => 'Tracking i shipping webhookovi', 'is_active' => false, 'group' => 'Shipping'],
        ['id' => 4, 'name' => 'featured_landing_blocks', 'description' => 'Growth promo sekcije na homepageu', 'is_active' => false, 'group' => 'Growth'],
    ];

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:100', 'regex:/^[a-z_]+$/'],
            'description' => ['nullable', 'string', 'max:255'],
            'isActive' => ['boolean'],
        ]);

        if (Schema::hasTable('feature_flags')) {
            FeatureFlag::query()->updateOrCreate(
                ['name' => $this->name],
                ['description' => $this->description, 'is_active' => $this->isActive]
            );

            Cache::forget("feature_flag:{$this->name}");
            $this->feedback = "Feature flag '{$this->name}' je sačuvan.";
        } else {
            $this->fallbackFlags[] = [
                'id' => count($this->fallbackFlags) + 1,
                'name' => $this->name,
                'description' => $this->description,
                'is_active' => $this->isActive,
                'group' => $this->guessGroup($this->name),
            ];

            $this->feedback = "Demo feature flag '{$this->name}' je dodan u UI.";
        }

        $this->reset(['name', 'description', 'isActive']);
    }

    public function toggle(int|string $flagId): void
    {
        if (Schema::hasTable('feature_flags')) {
            $flag = FeatureFlag::query()->find($flagId);

            if (! $flag) {
                return;
            }

            $flag->update(['is_active' => ! $flag->is_active]);
            Cache::forget("feature_flag:{$flag->name}");
            $this->feedback = "Feature flag '{$flag->name}' je ".($flag->fresh()->is_active ? 'aktiviran' : 'deaktiviran').'.';

            return;
        }

        $this->fallbackFlags = collect($this->fallbackFlags)
            ->map(function (array $flag) use ($flagId) {
                if ((string) $flag['id'] !== (string) $flagId) {
                    return $flag;
                }

                $flag['is_active'] = ! $flag['is_active'];

                return $flag;
            })
            ->all();

        $this->feedback = 'Demo feature flag status je ažuriran.';
    }

    /**
     * @return mixed
     */
    public function getGroupedFlagsProperty()
    {
        $flags = collect($this->fallbackFlags);

        if (Schema::hasTable('feature_flags')) {
            $databaseFlags = FeatureFlag::query()
                ->orderBy('name')
                ->get()
                ->map(fn (FeatureFlag $flag): array => [
                    'id' => $flag->id,
                    'name' => $flag->name,
                    'description' => $flag->description,
                    'is_active' => $flag->is_active,
                    'group' => $this->guessGroup($flag->name),
                ]);

            if ($databaseFlags->isNotEmpty()) {
                $flags = $databaseFlags;
            }
        }

        return $flags->groupBy('group');
    }

    public function render(): View
    {
        return view('livewire.admin.feature-flags');
    }

    protected function guessGroup(string $name): string
    {
        return match (true) {
            str_contains($name, 'bid') || str_contains($name, 'auction') => 'Bidding',
            str_contains($name, 'wallet') || str_contains($name, 'payment') || str_contains($name, 'escrow') => 'Payments',
            str_contains($name, 'ship') || str_contains($name, 'courier') || str_contains($name, 'tracking') => 'Shipping',
            default => 'Growth',
        };
    }
}
