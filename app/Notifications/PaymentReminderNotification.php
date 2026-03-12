<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urgency = $this->daysRemaining <= 1 ? 'urgent' : 'normal';
        
        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Pozdrav ' . $notifiable->name . '!')
            ->line($this->getMessageBody())
            ->line('')
            ->line('**Narudžba:** #' . $this->order->id)
            ->line('**Artikal:** ' . $this->order->auction->title)
            ->line('**Iznos:** ' . number_format($this->order->total_amount, 2) . ' KM')
            ->line('**Rok plaćanja:** ' . $this->order->payment_deadline_at->format('d.m.Y. H:i'))
            ->line('')
            ->line($this->daysRemaining <= 1 
                ? '⚠️ Ovo je zadnji podsjetnik. Nakon isteka roka, narudžba će biti automatski otkazana.'
                : 'Molimo izvršite plaćanje što prije kako bi prodavac mogao poslati artikal.')
            ->action('Plati odmah', route('orders.pay', $this->order))
            ->salutation('Tim Aukcije.ba');
    }

    protected function getSubject(): string
    {
        if ($this->daysRemaining <= 1) {
            return '⚠️ ZADNJI POZIV - Plaćanje narudžbe #' . $this->order->id;
        }
        
        return 'Podsjetnik: Plaćanje narudžbe #' . $this->order->id;
    }

    protected function getMessageBody(): string
    {
        if ($this->daysRemaining <= 1) {
            return 'Hitno! Imate još manje od 24 sata za plaćanje narudžbe.';
        }
        
        return 'Ovo je podsjetnik da niste izvršili plaćanje za vašu narudžbu.';
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_reminder',
            'order_id' => $this->order->id,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
