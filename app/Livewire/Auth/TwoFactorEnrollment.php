<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class TwoFactorEnrollment extends Component
{
    public bool $alreadyEnabled = false;

    public int $step = 1;

    public string $qrCodeSecret = '';

    public string $code = '';

    /** @var list<string> */
    public array $backupCodes = [];

    public string $statusMessage = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->alreadyEnabled = (bool) ($user?->two_factor_secret && $user?->two_factor_confirmed_at);
        $this->qrCodeSecret = strtoupper(Str::random(16));
        $this->backupCodes = $this->generateBackupCodes();
    }

    public function nextStep(): void
    {
        $this->step = min(2, $this->step + 1);
    }

    public function previousStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function verifyCode(): void
    {
        $this->code = preg_replace('/\D+/', '', $this->code) ?? '';
    }

    public function enable2FA(): void
    {
        $this->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->forceFill([
            'two_factor_secret' => $this->qrCodeSecret,
            'two_factor_recovery_codes' => json_encode($this->backupCodes, JSON_THROW_ON_ERROR),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->alreadyEnabled = true;
        $this->step = 3;
        $this->statusMessage = 'Dvofaktorska zaštita je aktivirana.';
    }

    public function disable2FA(): void
    {
        $user = Auth::user();
        if (! $user) {
            return;
        }

        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->alreadyEnabled = false;
        $this->step = 1;
        $this->qrCodeSecret = strtoupper(Str::random(16));
        $this->backupCodes = $this->generateBackupCodes();
        $this->statusMessage = 'Dvofaktorska zaštita je isključena.';
    }

    public function downloadBackupCodes(): void
    {
        $this->statusMessage = 'Sačuvaj backup kodove na sigurnom mjestu.';
    }

    public function finish(): void
    {
        $this->redirectRoute('settings.security');
    }

    public function render(): View
    {
        return view('livewire.auth.2fa-enrollment');
    }

    /**
     * @return list<string>
     */
    private function generateBackupCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn (): string => strtoupper(Str::random(4)).'-'.strtoupper(Str::random(4)))
            ->all();
    }
}
