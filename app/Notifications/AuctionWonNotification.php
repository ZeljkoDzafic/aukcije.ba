<?php

namespace App\Notifications;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AuctionWonNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Auction $auction,
        public float $finalPrice
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database', 'broadcast', 'mail'];
        
        if ($notifiable->prefersSmsNotification()) {
            $channels[] = 'sms';
        }
        
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Čestitamo! Dobili ste aukciju: ' . $this->auction->title)
            ->greeting('Čestitamo ' . $notifiable->name . '!')
            ->line('Pobijedili ste na aukciji!')
            ->line('**Aukcija:** ' . $this->auction->title)
            ->line('**Finalna cijena:** ' . number_format($this->finalPrice, 2) . ' KM')
            ->line('**Prodavac:** ' . $this->auction->seller->name)
            ->line('')
            ->line('Sljedeći koraci:')
            ->line('1. Otiđite na "Moje narudžbe"')
            ->line('2. Izvršite plaćanje u roku od 3 dana')
            ->line('3. Nakon potvrde dostave, sredstva se oslobađaju prodavcu')
            ->action('Plati odmah', route('orders.show', ['order' => $this->auction->order?->id]))
            ->salutation('Čestitamo još jednom! Tim Aukcije.ba');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage)
            ->content('🎉 Čestitamo! Dobili ste aukciju: ' . $this->auction->title)
            ->level('success')
            ->data([
                'auction_id' => $this->auction->id,
                'auction_title' => $this->auction->title,
                'final_price' => $this->finalPrice,
                'order_id' => $this->auction->order?->id,
            ]);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'auction_won',
            'auction_id' => $this->auction->id,
            'auction_title' => $this->auction->title,
            'auction_image' => $this->auction->primary_image?->url,
            'final_price' => $this->finalPrice,
            'order_id' => $this->auction->order?->id,
        ];
    }
}
