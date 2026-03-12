<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * T-1400: GDPR data export — compile all user PII into a ZIP and send download link by email.
 *
 * Dispatched from GDPRController when user requests their data.
 * Link expires in 24 hours.
 */
class GDPRDataExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(private readonly User $user) {}

    public function handle(): void
    {
        $data = $this->compileUserData();

        $json     = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $filename = "gdpr_export_{$this->user->id}_" . now()->format('Ymd_His') . '.json';
        $path     = "gdpr-exports/{$filename}";

        Storage::disk('s3')->put($path, $json, [
            'visibility' => 'private',
        ]);

        $downloadUrl = Storage::disk('s3')->temporaryUrl($path, now()->addHours(24));

        // Send email with download link
        Mail::raw(
            "Poštovani {$this->user->name},\n\n"
            . "Vaš GDPR izvoz podataka je spreman. Preuzmite ga na sljedećem linku (link ističe za 24 sata):\n\n"
            . $downloadUrl . "\n\n"
            . "Ukoliko niste tražili ovaj izvoz, kontaktirajte podršku.",
            fn ($message) => $message
                ->to($this->user->email)
                ->subject('Vaši podaci — GDPR izvoz'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function compileUserData(): array
    {
        $user = $this->user;

        return [
            'exported_at' => now()->toIso8601String(),
            'profile'     => [
                'id'                => $user->id,
                'name'              => $user->name,
                'email'             => $user->email,
                'phone'             => $user->phone,
                'kyc_level'         => $user->kyc_level,
                'created_at'        => $user->created_at?->toIso8601String(),
                'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            ],
            'bids'  => DB::table('bids')
                ->where('user_id', $user->id)
                ->select(['id', 'auction_id', 'amount', 'is_winning', 'created_at'])
                ->get(),
            'orders' => DB::table('orders')
                ->where('buyer_id', $user->id)
                ->orWhere('seller_id', $user->id)
                ->select(['id', 'auction_id', 'status', 'total_amount', 'created_at'])
                ->get(),
            'wallet_transactions' => DB::table('wallet_transactions')
                ->join('wallets', 'wallets.id', '=', 'wallet_transactions.wallet_id')
                ->where('wallets.user_id', $user->id)
                ->select(['wallet_transactions.id', 'type', 'amount', 'description', 'wallet_transactions.created_at'])
                ->get(),
            'saved_searches' => DB::table('saved_searches')
                ->where('user_id', $user->id)
                ->select(['id', 'name', 'query', 'created_at'])
                ->get(),
            'watchlist' => DB::table('auction_watchers')
                ->where('user_id', $user->id)
                ->select(['auction_id', 'created_at'])
                ->get(),
            'ratings_given' => DB::table('user_ratings')
                ->where('rater_id', $user->id)
                ->select(['id', 'rated_id', 'score', 'comment', 'created_at'])
                ->get(),
        ];
    }
}
