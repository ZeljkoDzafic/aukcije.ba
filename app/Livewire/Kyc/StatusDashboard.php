<?php

declare(strict_types=1);

namespace App\Livewire\Kyc;

use App\Models\UserVerification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class StatusDashboard extends Component
{
    public string $statusMessage = '';

    /** @var array<int, string> */
    public array $levelNames = [
        0 => 'Bez verifikacije',
        1 => 'Email',
        2 => 'SMS',
        3 => 'Dokument',
    ];

    public function sendSmsOtp(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if (! $user->phone) {
            $this->statusMessage = 'Dodaj broj telefona prije SMS verifikacije.';

            return;
        }

        $user->forceFill([
            'phone_verified_at' => now(),
            'kyc_level' => max(2, (int) $user->kyc_level),
        ])->save();

        $this->statusMessage = 'Telefon je označen kao verifikovan za lokalno okruženje.';
    }

    public function openDocumentUpload(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        if (Schema::hasTable('user_verifications')) {
            UserVerification::query()->firstOrCreate(
                ['user_id' => $user->id, 'type' => 'identity_document'],
                ['status' => 'pending']
            );
        }

        $this->statusMessage = 'Zahtjev za dokument verifikaciju je evidentiran i čeka pregled.';
    }

    public function render(): View
    {
        return view('livewire.kyc.status-dashboard', [
            'user' => Auth::user(),
            'levelNames' => $this->levelNames,
        ]);
    }
}
