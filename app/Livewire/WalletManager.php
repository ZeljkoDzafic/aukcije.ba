<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WalletManager extends Component
{
    public string $depositAmount = '';

    public string $withdrawAmount = '';

    public string $gateway = 'stripe';

    public string $filter = 'all';

    public string $feedback = '';

    public string $errorMessage = '';

    public function quickDeposit(int $amount): void
    {
        $this->depositAmount = (string) $amount;
    }

    public function deposit(WalletService $walletService): void
    {
        $this->reset(['feedback', 'errorMessage']);
        $this->validate([
            'depositAmount' => ['required', 'numeric', 'min:1'],
            'gateway' => ['required', 'string'],
        ]);

        try {
            $walletService->deposit(Auth::user(), (float) $this->depositAmount, $this->gateway);
            $this->feedback = 'Wallet dopuna je evidentirana.';
            $this->depositAmount = '';
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function withdraw(WalletService $walletService): void
    {
        $this->reset(['feedback', 'errorMessage']);
        $this->validate([
            'withdrawAmount' => ['required', 'numeric', 'min:1'],
        ]);

        try {
            $walletService->withdraw(Auth::user(), (float) $this->withdrawAmount);
            $this->feedback = 'Zahtjev za isplatu je evidentiran.';
            $this->withdrawAmount = '';
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function getWalletProperty(): ?Wallet
    {
        return Auth::user()?->wallet;
    }

    /**
     * @return Collection<int, WalletTransaction>|EloquentCollection<int, WalletTransaction>
     */
    public function getTransactionsProperty(): Collection|EloquentCollection
    {
        $wallet = $this->getWalletProperty();

        if (! $wallet) {
            return collect();
        }

        return $wallet->transactions()
            ->latest('created_at')
            ->get()
            ->when($this->filter !== 'all', fn ($items) => $items->where('type', $this->filter))
            ->values();
    }

    public function render(): View
    {
        return view('livewire.wallet-manager');
    }
}
