<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Wallet;
use App\Services\PaymentService;
use App\Services\WalletService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WalletManager extends Component
{
    use WithPagination;

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

    public function deposit(PaymentService $paymentService): void
    {
        $this->reset(['feedback', 'errorMessage']);
        $this->validate([
            'depositAmount' => ['required', 'numeric', 'min:1', 'max:10000'],
            'gateway'       => ['required', 'string', 'in:stripe,monri,corvuspay'],
        ]);

        try {
            $result = $paymentService->initiateDeposit(
                Auth::user(),
                (float) $this->depositAmount,
                $this->gateway,
                url('/user/wallet')
            );

            if (! $result['success']) {
                $this->errorMessage = $result['error'] ?? 'Greška pri inicijalizaciji plaćanja.';

                return;
            }

            if (isset($result['redirect_url'])) {
                $this->redirect($result['redirect_url']);

                return;
            }

            // Gateway processed synchronously (e.g. local/test gateway)
            $this->feedback = 'Dopuna je evidentirana.';
            $this->depositAmount = '';
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function withdraw(WalletService $walletService): void
    {
        $this->reset(['feedback', 'errorMessage']);
        $this->validate([
            'withdrawAmount' => ['required', 'numeric', 'min:10'],
        ]);

        try {
            $walletService->withdraw(Auth::user(), (float) $this->withdrawAmount);
            $this->feedback = 'Zahtjev za isplatu je evidentiran.';
            $this->withdrawAmount = '';
        } catch (\Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
        }
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function getWalletProperty(): ?Wallet
    {
        return Auth::user()?->wallet;
    }

    public function getTransactionsProperty(): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $wallet = $this->getWalletProperty();

        if (! $wallet) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return $wallet->transactions()
            ->when($this->filter !== 'all', fn ($q) => $q->where('type', $this->filter))
            ->latest('created_at')
            ->paginate(20);
    }

    public function render(): View
    {
        return view('livewire.wallet-manager');
    }
}
