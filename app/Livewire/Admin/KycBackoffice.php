<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\UserVerification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class KycBackoffice extends Component
{
    public string $statusFilter = 'pending';

    public bool $showDocumentViewer = false;

    public string $selectedDocument = '';

    public string $statusMessage = '';

    public function viewDocument(string $documentUrl): void
    {
        $this->selectedDocument = $documentUrl;
        $this->showDocumentViewer = true;
    }

    public function closeDocumentViewer(): void
    {
        $this->showDocumentViewer = false;
        $this->selectedDocument = '';
    }

    public function approveKyc(string $verificationId): void
    {
        $this->review($verificationId, 'approved');
    }

    public function rejectKyc(string $verificationId): void
    {
        $this->review($verificationId, 'rejected');
    }

    public function render(): View
    {
        $pendingKyc = collect();

        if (Schema::hasTable('user_verifications')) {
            $pendingKyc = UserVerification::query()
                ->with(['user.profile', 'reviewer'])
                ->where('status', $this->statusFilter)
                ->latest()
                ->get()
                ->map(function (UserVerification $verification) {
                    $documents = collect([$verification->document_url, $verification->document_back_url])
                        ->filter()
                        ->values()
                        ->all();

                    $verification->setAttribute('documents', collect($documents)->map(fn (string $url) => (object) ['url' => $url]));

                    return $verification;
                });
        }

        return view('livewire.admin.kyc-backoffice', [
            'pendingKyc' => $pendingKyc,
        ]);
    }

    private function review(string $verificationId, string $status): void
    {
        if (! Schema::hasTable('user_verifications')) {
            return;
        }

        $verification = UserVerification::query()->with('user')->find($verificationId);

        if (! $verification) {
            $this->statusMessage = 'KYC zapis nije pronađen.';

            return;
        }

        $verification->forceFill([
            'status' => $status,
            'reviewer_id' => Auth::id(),
            'verified_at' => $status === 'approved' ? now() : null,
            'rejection_reason' => $status === 'rejected' ? 'Potrebna je dopuna dokumentacije.' : null,
            'notes' => $status === 'approved' ? 'Pregled završen bez primjedbi.' : 'Zatražena je dopuna dokumentacije.',
        ])->save();

        if ($status === 'approved' && $verification->user) {
            $verification->user->forceFill([
                'kyc_level' => max(3, (int) $verification->user->kyc_level),
            ])->save();
        }

        $this->statusMessage = $status === 'approved'
            ? 'KYC zahtjev je odobren.'
            : 'KYC zahtjev je odbijen.';
    }
}
