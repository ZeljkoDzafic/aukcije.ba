<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeatureFlag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class FeatureFlagController extends Controller
{
    public function index(): View
    {
        return view('pages.admin.feature-flags.index');
    }

    public function toggle(Request $request, FeatureFlag $flag): RedirectResponse
    {
        $flag->update(['is_active' => ! $flag->is_active]);
        Cache::forget("feature_flag:{$flag->name}");

        $status = $flag->fresh()->is_active ? 'aktivirana' : 'deaktivirana';

        return redirect()->back()->with('success', "Funkcionalnost '{$flag->name}' je {$status}.");
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:feature_flags,name', 'regex:/^[a-z_]+$/'],
            'description' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        FeatureFlag::create($validated);

        return redirect()->back()->with('success', "Nova funkcionalnost '{$validated['name']}' kreirana.");
    }

    public function destroy(FeatureFlag $flag): RedirectResponse
    {
        Cache::forget("feature_flag:{$flag->name}");
        $flag->delete();

        return redirect()->back()->with('success', "Funkcionalnost '{$flag->name}' obrisana.");
    }
}
