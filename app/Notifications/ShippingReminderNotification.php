<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ShippingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public int $daysSincePayment
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urgency = $this->daysSincePayment >= 4 ? 'urgent' : 'normal';

        return (new MailMessage)
            ->subject($this->getSubject())
            ->greeting('Pozdrav '.$notifiable->name.'!')
            ->line($this->getMessageBody())
            ->line('')
            ->line('**Narudžba:** #'.$this->order->id)
            ->line('**Artikal:** '.$this->order->auction->title)
            ->line('**Kupac:** '.$this->order->buyer->name)
            ->line('**Plaćanje primljeno:** '.$this->order->paid_at->format('d.m.Y.'))
            ->line('')
            ->line($this->daysSincePayment >= 4
                ? '⚠️ Hitno! Prošlo je 5 dana od plaćanja. Molimo odmah pošaljite artikal.'
                : 'Molimo pošaljite artikal što prije kako bi kupac mogao primiti pošiljku.')
            ->action('Označi kao poslano', route('seller.orders.ship', $this->order))
            ->salutation('Tim Aukcije.ba');
    }

    protected function getSubject(): string
    {
        if ($this->daysSincePayment >= 4) {
            return '⚠️ HITNO - Pošaljite artikal za narudžbu #'.$this->order->id;
        }

        return 'Podsjetnik: Pošaljite artikal za narudžbu #'.$this->order->id;
    }

    protected function getMessageBody(): string
    {
        if ($this->daysSincePayment >= 4) {
            return 'Upozorenje: Niste poslali artikal u roku od 5 dana. Ovo može utjecati na vašu reputaciju.';
        }

        return 'Ovo je podsjetnik da niste označili narudžbu kao poslanu.';
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'shipping_reminder',
            'order_id' => $this->order->id,
            'days_since_payment' => $this->daysSincePayment,
        ];
    }
}
