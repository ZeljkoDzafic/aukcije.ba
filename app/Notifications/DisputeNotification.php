<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class DisputeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const TYPE_OPENED = 'opened';
    public const TYPE_ESCALATED = 'escalated';
    public const TYPE_RESOLVED = 'resolved';
    public const TYPE_EVIDENCE_REQUESTED = 'evidence_requested';

    public function __construct(
        public Dispute $dispute,
        public string $type = self::TYPE_OPENED,
        public ?string $message = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subject = match($this->type) {
            self::TYPE_OPENED => 'Otvoren je spor - Narudžba #' . $this->dispute->order->id,
            self::TYPE_ESCALATED => 'Spor je eskaliran - Potrebna administracija',
            self::TYPE_RESOLVED => 'Spor je riješen - Narudžba #' . $this->dispute->order->id,
            self::TYPE_EVIDENCE_REQUESTED => 'Potrebni dokazi za spor - Narudžba #' . $this->dispute->order->id,
            default => 'Obavijest o sporu'
        };

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Pozdrav ' . $notifiable->name . '!')
            ->line($this->getMessageBody());

        if ($this->type === self::TYPE_OPENED || $this->type === self::TYPE_ESCALATED) {
            $mail->action('Pogledaj spor', route('disputes.show', $this->dispute));
        }

        return $mail->salutation('Tim Aukcije.ba');
    }

    protected function getMessageBody(): string
    {
        if ($this->message) {
            return $this->message;
        }

        return match($this->type) {
            self::TYPE_OPENED => "Kupac je otvorio spor za narudžbu #{$this->dispute->order->id}. Razlog: {$this->dispute->reason}",
            self::TYPE_ESCALATED => "Spor je eskaliran. Administrator će pregledati slučaj u roku od 5 dana.",
            self::TYPE_RESOLVED => "Spor je riješen. Ishod: {$this->dispute->resolution}",
            self::TYPE_EVIDENCE_REQUESTED => "Molimo dostavite dokaze u roku od 48 sati.",
            default => 'Imate novu obavijest o sporu.'
        };
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage)
            ->content($this->getMessageBody())
            ->level($this->getLevel())
            ->data([
                'dispute_id' => $this->dispute->id,
                'order_id' => $this->dispute->order->id,
                'type' => $this->type,
            ]);
    }

    protected function getLevel(): string
    {
        return match($this->type) {
            self::TYPE_OPENED => 'warning',
            self::TYPE_ESCALATED => 'error',
            self::TYPE_RESOLVED => 'success',
            self::TYPE_EVIDENCE_REQUESTED => 'warning',
            default => 'info'
        };
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'dispute_' . $this->type,
            'dispute_id' => $this->dispute->id,
            'order_id' => $this->dispute->order->id,
            'reason' => $this->dispute->reason,
            'resolution' => $this->dispute->resolution,
        ];
    }
}
