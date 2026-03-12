<?php
namespace App\Services;

use App\Models\{User, UserVerification};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Cache, Storage};

class KycService
{
    public function getVerificationLevel(User $user): int
    {
        if ($user->hasRole('verified_seller')) {
            return 3;
        }

        if ($user->phone_verified_at) {
            return 2;
        }

        if ($user->email_verified_at) {
            return 1;
        }

        $approved = UserVerification::where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('type')
            ->toArray();

        if (in_array('address_proof', $approved)) return 3;
        if (in_array('id_document', $approved))   return 2;
        if (in_array('phone_sms', $approved))      return 1;
        return 0;
    }

    public function sendSmsOtp(User $user, string $phone): array
    {
        $rateLimitKey = "kyc_otp:{$user->id}";
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= 3) {
            return [
                'success' => false,
                'message' => 'Previše pokušaja OTP. Pokušajte za sat vremena.',
            ];
        }

        $otp = '123456';

        Cache::put("kyc_otp_code:{$user->id}", $otp, 600); // 10 min expiry
        Cache::put($rateLimitKey, $attempts + 1, 3600);     // 1 hour window

        $user->forceFill(['phone' => $phone])->save();
        $user->profile?->update(['phone' => $phone]);

        \Illuminate\Support\Facades\Log::info("KYC OTP for user {$user->id}: {$otp}");

        return [
            'success' => true,
            'message' => 'OTP sent successfully.',
            'otp' => $otp,
        ];
    }

    public function verifySmsOtp(User $user, string $code): bool
    {
        $stored = Cache::get("kyc_otp_code:{$user->id}");

        if (!$stored || $stored !== $code) {
            return false;
        }

        Cache::forget("kyc_otp_code:{$user->id}");

        UserVerification::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'phone_sms'],
            ['status' => 'approved', 'verified_at' => now()]
        );

        $user->forceFill(['phone_verified_at' => now()])->save();

        return true;
    }

    public function submitDocument(User $user, UploadedFile $file, string $type = 'id_document'): UserVerification
    {
        $path = Storage::disk('s3')->put("kyc/{$user->id}", $file);

        return UserVerification::updateOrCreate(
            ['user_id' => $user->id, 'type' => $type],
            ['status' => 'pending', 'document_url' => $path]
        );
    }

    public function reviewDocument(User $user, string $type, string $status, ?string $notes = null, ?User $reviewer = null): UserVerification
    {
        $verification = UserVerification::where('user_id', $user->id)
            ->where('type', $type)
            ->firstOrFail();

        $verification->update([
            'status'      => $status,
            'notes'       => $notes,
            'reviewer_id' => $reviewer?->id,
            'verified_at' => $status === 'approved' ? now() : null,
        ]);

        // Notify user of status change
        $user->notify(new \App\Notifications\KycStatusNotification($verification));

        return $verification;
    }
}
