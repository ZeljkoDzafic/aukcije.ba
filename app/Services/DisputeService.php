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
                'order_id'    => $order->id,
                'opened_by'   => $opener->id,
                'reason'      => $reason,
                'description' => $description,
                'status'      => 'open',
            ]);

            $order->update(['status' => 'disputed']);

            // Notify seller
            $order->seller?->notify(
                new \App\Notifications\DisputeNotification($dispute, 'opened')
            );

            return $dispute;
        });
    }

    public function addEvidence(Dispute $dispute, User $user, array $files): array
    {
        $paths = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $paths[] = Storage::disk('s3')->put("disputes/{$dispute->id}", $file);
            }
        }

        // Store paths in dispute metadata (extend if needed)
        $existing = json_decode($dispute->resolution ?? '{}', true);
        $existing['evidence'][$user->id][] = $paths;
        $dispute->update(['resolution' => json_encode($existing)]);

        return $paths;
    }

    public function resolve(Dispute $dispute, string $resolution, User $resolver): Dispute
    {
        DB::transaction(function () use ($dispute, $resolution, $resolver) {
            $dispute->update([
                'status'      => 'resolved',
                'resolution'  => $resolution,
                'resolved_by' => $resolver->id,
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

        return $dispute->fresh();
    }
}
