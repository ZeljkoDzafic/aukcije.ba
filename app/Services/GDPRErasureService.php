<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * T-1401: GDPR erasure — anonymise a user's PII while preserving legal records.
 *
 * We do NOT delete financial records (orders, wallet transactions, bids)
 * as these may be required for tax/compliance purposes. Instead we
 * replace all personally-identifiable data with opaque placeholders.
 */
class GDPRErasureService
{
    /**
     * Anonymise a user account.
     * After this call the user cannot log in and all PII is wiped.
     */
    public function anonymise(User $user): void
    {
        DB::transaction(function () use ($user) {
            $handle = 'deleted_' . Str::random(12);

            // Core user fields
            $user->update([
                'name'                    => $handle,
                'email'                   => $handle . '@deleted.invalid',
                'password'                => bcrypt(Str::random(64)),
                'phone'                   => null,
                'email_verified_at'       => null,
                'phone_verified_at'       => null,
                'two_factor_secret'       => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
                'notification_preferences' => null,
                'is_banned'               => true,
                'ban_reason'              => 'account_deleted_gdpr',
            ]);

            // Profile
            $user->profile?->update([
                'first_name'    => $handle,
                'last_name'     => '',
                'bio'           => null,
                'address'       => null,
                'city'          => null,
                'country'       => null,
                'avatar'        => null,
                'date_of_birth' => null,
            ]);

            // Revoke all API tokens
            $user->tokens()->delete();

            // Remove saved searches
            DB::table('saved_searches')->where('user_id', $user->id)->delete();

            // Anonymise messages (replace body with placeholder)
            DB::table('messages')
                ->where('sender_id', $user->id)
                ->update(['body' => '[Message deleted — GDPR]']);

            // Anonymise auction titles/descriptions for auctions with no bids
            $user->auctions()
                ->where('bids_count', 0)
                ->each(function ($auction) {
                    $auction->update([
                        'title'       => '[Deleted auction]',
                        'description' => null,
                    ]);
                });

            Log::info("GDPR erasure completed for user {$user->id}");
        });
    }

    /**
     * Check if a user is eligible for erasure (no open disputes, no pending payments).
     */
    public function isEligible(User $user): bool
    {
        $hasOpenDispute = DB::table('disputes')
            ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->whereIn('status', ['open', 'in_review'])
            ->exists();

        if ($hasOpenDispute) {
            return false;
        }

        $hasPendingOrder = DB::table('orders')
            ->where(fn ($q) => $q->where('buyer_id', $user->id)->orWhere('seller_id', $user->id))
            ->whereIn('status', ['pending_payment', 'paid', 'shipped'])
            ->exists();

        return ! $hasPendingOrder;
    }
}
