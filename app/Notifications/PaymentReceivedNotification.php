<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Payment $payment
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
        return (new MailMessage)
            ->subject('Plaćanje primljeno - Narudžba #'.$this->order->id)
            ->greeting('Pozdrav '.$notifiable->name.'!')
            ->line('Primili smo plaćanje za vašu narudžbu.')
            ->line('**Broj narudžbe:** #'.$this->order->id)
            ->line('**Iznos:** '.number_format((float) $this->payment->amount, 2).' KM')
            ->line('**Način plaćanja:** '.$this->payment->gateway)
            ->line('')
            ->line('Prodavac će biti obaviješten i treba poslati artikal u roku od 5 dana.')
            ->action('Pogledaj narudžbu', route('orders.show', $this->order))
            ->salutation('Hvala na kupovini! Tim Aukcije.ba');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'payment_received',
            'message' => 'Plaćanje primljeno za narudžbu #'.$this->order->id,
            'level' => 'success',
            'order_id' => $this->order->id,
            'payment_amount' => (float) $this->payment->amount,
            'payment_gateway' => $this->payment->gateway,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_received',
            'order_id' => $this->order->id,
            'payment_amount' => $this->payment->amount,
            'payment_gateway' => $this->payment->gateway,
        ];
    }
}
