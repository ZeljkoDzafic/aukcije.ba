<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Shipment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class ItemShippedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Order $order,
        public Shipment $shipment
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
            ->subject('Vaš artikal je poslan - Narudžba #' . $this->order->id)
            ->greeting('Pozdrav ' . $notifiable->name . '!')
            ->line('Prodavac je poslao vaš artikal!')
            ->line('**Broj narudžbe:** #' . $this->order->id)
            ->line('**Kurir:** ' . $this->shipment->courier_name)
            ->line('**Tracking broj:** ' . $this->shipment->tracking_number)
            ->line('')
            ->line('Možete pratiti pošiljku na linku ispod.')
            ->action('Prati pošiljku', $this->shipment->tracking_url)
            ->line('Nakon primitka, molimo potvrdite dostavu.')
            ->salutation('Tim Aukcije.ba');
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return (new BroadcastMessage)
            ->content('Artikal je poslan! Tracking: ' . $this->shipment->tracking_number)
            ->level('info')
            ->data([
                'order_id' => $this->order->id,
                'courier_name' => $this->shipment->courier_name,
                'tracking_number' => $this->shipment->tracking_number,
                'tracking_url' => $this->shipment->tracking_url,
            ]);
    }

    public function toSms(object $notifiable): SmsMessage
    {
        return (new SmsMessage)
            ->content(
                "AUKCIJA.BA: Vaš artikal je poslan! " .
                "Kurir: {$this->shipment->courier_name}. " .
                "Tracking: {$this->shipment->tracking_number}. " .
                "Prati: {$this->shipment->tracking_url}"
            );
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'item_shipped',
            'order_id' => $this->order->id,
            'courier_name' => $this->shipment->courier_name,
            'tracking_number' => $this->shipment->tracking_number,
            'tracking_url' => $this->shipment->tracking_url,
        ];
    }
}
