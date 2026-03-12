<?php

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OutbidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Auction $auction,
        public readonly float $newAmount,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Nadlicitiran/a si na: {$this->auction->title}")
            ->greeting('Pozdrav ' . $notifiable->name . '!')
            ->line("Neko je ponudio " . number_format($this->newAmount, 2) . " BAM na aukciji '{$this->auction->title}'.")
            ->action('Vrati se i licitiraj', url("/aukcije/{$this->auction->id}"))
            ->line('Iskoristi proxy bid da automatski povećaš svoju ponudu!')
            ->salutation('Tim Aukcije.ba');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => 'outbid',
            'auction_id'    => $this->auction->id,
            'auction_title' => $this->auction->title,
            'new_amount'    => $this->newAmount,
            'message'       => "Nadlicitirani ste na aukciji {$this->auction->title}.",
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
}
