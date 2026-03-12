<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ADDITIONAL_INFO = 'additional_info';

    public function __construct(
        public string $status,
        public ?string $message = null,
        public ?int $kycLevel = null
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->status) {
            self::STATUS_PENDING => 'KYC verifikacija u toku',
            self::STATUS_APPROVED => 'KYC verifikacija odobrena - Čestitamo!',
            self::STATUS_REJECTED => 'KYC verifikacija odbijena',
            self::STATUS_ADDITIONAL_INFO => 'Potrebni dodatni dokumenti',
            default => 'Status KYC verifikacije'
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Pozdrav '.$notifiable->name.'!')
            ->line($this->getMessageBody());

        if ($this->status === self::STATUS_APPROVED) {
            $mail->line('')
                ->line('Sada možete:')
                ->line('- Kreirati do 50 aukcija mjesečno')
                ->line('- Dobiti "Verified Seller" badge')
                ->line('- Imati nižu proviziju')
                ->action('Kreiraj aukciju', route('seller.auctions.create'));
        } elseif ($this->status === self::STATUS_REJECTED || $this->status === self::STATUS_ADDITIONAL_INFO) {
            $mail->action('Pogledaj zahtjev', route('kyc.status'));
        }

        return $mail->salutation('Tim Aukcije.ba');
    }

    protected function getMessageBody(): string
    {
        if ($this->message) {
            return $this->message;
        }

        return match ($this->status) {
            self::STATUS_PENDING => 'Vaš zahtjev za KYC verifikaciju se obrađuje. Obavijestit ćemo vas kada bude gotovo.',
            self::STATUS_APPROVED => "Čestitamo! Vaša KYC verifikacija je odobrena. Vaš nivo verifikacije je: {$this->kycLevel}.",
            self::STATUS_REJECTED => 'Vaš zahtjev za KYC verifikaciju je odbijen. Molimo provjerite razlog i pokušajte ponovo.',
            self::STATUS_ADDITIONAL_INFO => 'Potrebni su dodatni dokumenti za kompletiranje vaše KYC verifikacije.',
            default => 'Status vaše KYC verifikacije je promijenjen.'
        };
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'kyc_'.$this->status,
            'message' => $this->getMessageBody(),
            'level' => $this->getLevel(),
            'status' => $this->status,
            'kyc_level' => $this->kycLevel,
        ]);
    }

    protected function getLevel(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'error',
            self::STATUS_ADDITIONAL_INFO => 'warning',
            default => 'info'
        };
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'kyc_'.$this->status,
            'status' => $this->status,
            'kyc_level' => $this->kycLevel,
        ];
    }
}
