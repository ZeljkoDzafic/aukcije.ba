<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class DisputeResolution extends Component
{
    public ?string $disputeId = null;

    public string $resolution = '';

    public string $feedback = '';

    public string $message = '';

    /** @var list<array{author: string, body: string}> */
    public array $messages = [
        ['author' => 'Kupac', 'body' => 'Opis nije odgovarao stvarnom stanju artikla.'],
        ['author' => 'Seller', 'body' => 'Spreman sam na djelimični refund.'],
    ];

    public function resolve(string $action): void
    {
        $this->resolution = $action;

        if ($this->disputeId && Schema::hasTable('disputes')) {
            $dispute = Dispute::query()->find($this->disputeId);

            if ($dispute) {
                $payload = [
                    'status' => 'resolved',
                    'resolution' => $action,
                ];

                if (Schema::hasColumn('disputes', 'resolved_by')) {
                    $payload['resolved_by'] = Auth::id();
                }

                if (Schema::hasColumn('disputes', 'resolved_by_id')) {
                    $payload['resolved_by_id'] = Auth::id();
                }

                if (Schema::hasColumn('disputes', 'resolved_at')) {
                    $payload['resolved_at'] = now();
                }

                $dispute->update($payload);
                $this->feedback = "Spor '{$dispute->id}' je riješen akcijom: {$action}.";

                return;
            }
        }

        $this->feedback = "Pripremljena akcija: {$action}. Backend workflow još treba povezati.";
    }

    public function addMessage(): void
    {
        if ($this->message === '') {
            return;
        }

        if ($this->disputeId && Schema::hasTable('dispute_messages')) {
            DisputeMessage::query()->create([
                'dispute_id' => $this->disputeId,
                'user_id' => Auth::id(),
                'message' => $this->message,
                'is_from_admin' => true,
            ]);
        }

        $this->messages[] = [
            'author' => 'Admin',
            'body' => $this->message,
        ];

        $this->message = '';
        $this->feedback = 'Admin poruka je dodana u komunikacijski log.';
    }

    public function render(): View
    {
        if ($this->disputeId && Schema::hasTable('dispute_messages')) {
            $databaseMessages = DisputeMessage::query()
                ->where('dispute_id', $this->disputeId)
                ->with('user')
                ->latest()
                ->get()
                ->map(fn (DisputeMessage $message): array => [
                    'author' => $message->is_from_admin ? 'Admin' : ($message->user?->name ?? 'Korisnik'),
                    'body' => (string) $message->message,
                ])
                ->reverse()
                ->values()
                ->all();

            if ($databaseMessages !== []) {
                $this->messages = $databaseMessages;
            }
        }

        return view('livewire.admin.dispute-resolution');
    }
}
