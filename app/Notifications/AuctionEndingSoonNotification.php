<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionEndingSoonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Auction $auction,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if ($notifiable->prefersEmailNotification('ending_soon')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Aukcija završava uskoro: {$this->auction->title}")
            ->greeting('Pozdrav '.$notifiable->name.'!')
            ->line("Aukcija '{$this->auction->title}' završava za manje od 1 sat.")
            ->line('Trenutna cijena: '.number_format($this->auction->current_price, 2).' BAM')
            ->action('Licitiraj odmah', url("/aukcije/{$this->auction->id}"))
            ->salutation('Tim Aukcije.ba');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'auction_ending_soon',
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'ends_at' => $this->auction->ends_at?->toIso8601String(),
            'current_price' => $this->auction->current_price,
        ];
    }
}
