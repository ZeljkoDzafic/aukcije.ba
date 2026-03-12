<?php
namespace App\Services;

use App\Models\{Dispute, Order, User};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{DB, Storage};

class DisputeService
{
    protected EscrowService $escrowService;

    public function __construct(?EscrowService $escrowService = null)
    {
        $this->escrowService = $escrowService ?? app(EscrowService::class);
    }

    public function openDispute(Order $order, User $opener, string $reason, string $description): Dispute
    {
        return DB::transaction(function () use ($order, $opener, $reason, $description) {
            $dispute = Dispute::create([
                'order_id' => $order->id,
                'opened_by_id' => $opener->id,
                'reason' => $reason,
                'description' => $description,
                'status' => 'open',
            ]);

            $order->update(['status' => 'disputed']);

            // Notify seller
            $order->seller?->notify(
                new \App\Notifications\DisputeNotification($dispute, 'opened')
            );

            return $dispute;
        });
    }

    public function addEvidence(Dispute $dispute, User $user, array $files): bool
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = Storage::disk('s3')->put("disputes/{$dispute->id}", $file);
            } elseif (is_string($file)) {
                $paths[] = $file;
            }
        }

        $existing = $dispute->evidence ?? [];
        $existing[$user->id] = array_values(array_merge($existing[$user->id] ?? [], $paths));
        $dispute->update(['evidence' => $existing]);

        return true;
    }

    public function resolve(Dispute $dispute, string $resolution, User $resolver): bool
    {
        DB::transaction(function () use ($dispute, $resolution, $resolver) {
            $dispute->update([
                'status' => 'resolved',
                'resolution' => $resolution,
                'resolved_by_id' => $resolver->id,
                'resolved_at' => now(),
            ]);

            $order = $dispute->order;

            // If resolution is refund, refund buyer; otherwise release to seller
            if (str_contains(strtolower($resolution), 'refund')) {
                $this->escrowService->refundBuyer($order, $order->amount);
                $order->update(['status' => 'cancelled']);
            } else {
                $this->escrowService->releaseFunds($order);
                $order->update(['status' => 'completed']);
            }

            // Notify both parties
            $order->buyer?->notify(
                new \App\Notifications\DisputeNotification($dispute, 'resolved')
            );
            $order->seller?->notify(
                new \App\Notifications\DisputeNotification($dispute, 'resolved')
            );
        });

        return true;
    }

    public function autoEscalate(): int
    {
        $disputes = Dispute::query()
            ->where('status', 'open')
            ->where('created_at', '<=', now()->subHours(48))
            ->get();

        foreach ($disputes as $dispute) {
            $dispute->update([
                'status' => 'in_review',
                'escalated_at' => now(),
            ]);
        }

        return $disputes->count();
    }
}
