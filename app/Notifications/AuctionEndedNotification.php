<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Auction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuctionEndedNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isSold = $auction = $this->auction;
        $subject = $auction->status === 'sold'
            ? "Aukcija završena — prodano: {$auction->title}"
            : "Aukcija završena: {$auction->title}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting('Pozdrav '.$notifiable->name.'!')
            ->line("Aukcija '{$auction->title}' je završila.");

        if ($auction->status === 'sold') {
            $mail->line('Predmet je prodan za '.number_format($auction->current_price, 2).' BAM.')
                ->action('Pogledaj aukciju', url("/aukcije/{$auction->id}"));
        } else {
            $mail->line('Aukcija nije završila prodajom.')
                ->action('Pogledaj aukciju', url("/aukcije/{$auction->id}"));
        }

        return $mail->salutation('Tim Aukcije.ba');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'auction_ended',
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'status' => $this->auction->status,
            'final_price' => $this->auction->current_price,
        ];
    }
}
