<?php
namespace App\Services;

use App\Models\{User, UserVerification};
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Cache, Storage};

class KycService
{
    public function getVerificationLevel(User $user): int
    {
        $approved = UserVerification::where('user_id', $user->id)
            ->where('status', 'approved')
            ->pluck('type')
            ->toArray();

        if (in_array('address_proof', $approved)) return 3;
        if (in_array('id_document', $approved))   return 2;
        if (in_array('phone_sms', $approved))      return 1;
        return 0;
    }

    public function sendSmsOtp(User $user, string $phone): void
    {
        $rateLimitKey = "kyc_otp:{$user->id}";
        $attempts = Cache::get($rateLimitKey, 0);

        if ($attempts >= 3) {
            throw new \RuntimeException('Previše pokušaja OTP. Pokušajte za sat vremena.');
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Cache::put("kyc_otp_code:{$user->id}", $otp, 600); // 10 min expiry
        Cache::put($rateLimitKey, $attempts + 1, 3600);     // 1 hour window

        // Update phone on profile
        $user->profile?->update(['phone' => $phone]);

        // In production, send via Infobip/Twilio
        // SMS sending is handled by Qwen's NotificationService
        \Illuminate\Support\Facades\Log::info("KYC OTP for user {$user->id}: {$otp}");
    }

    public function verifySmsOtp(User $user, string $code): UserVerification
    {
        $stored = Cache::get("kyc_otp_code:{$user->id}");

        if (!$stored || $stored !== $code) {
            throw new \RuntimeException('Pogrešan ili istekao OTP kod.');
        }

        Cache::forget("kyc_otp_code:{$user->id}");

        return UserVerification::updateOrCreate(
            ['user_id' => $user->id, 'type' => 'phone_sms'],
            ['status' => 'approved', 'verified_at' => now()]
        );
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
