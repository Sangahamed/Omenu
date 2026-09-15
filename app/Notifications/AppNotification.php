<?php

namespace App\Notifications;

use App\Notifications\Channels\WebPushChannel;
use Illuminate\Notifications\Notification;

/**
 * Base commune a toutes les notifications applicatives.
 *
 * Une seule methode a definir (`payload()`), et la notification part a la
 * fois en base (cloche du header) et en Web Push (notification systeme du
 * navigateur). Le canal push s'ignore de lui-meme si les cles VAPID ne sont
 * pas configurees.
 */
abstract class AppNotification extends Notification
{
    /**
     * @return array{title:string, body:string, url:string, icon?:string, level?:string}
     */
    abstract public function payload(object $notifiable): array;

    public function via(object $notifiable): array
    {
        return ['database', WebPushChannel::class];
    }

    public function toArray(object $notifiable): array
    {
        return $this->payload($notifiable) + [
            'icon' => 'ri-notification-3-line',
            'level' => 'info',
        ];
    }

    /**
     * Charge utile lue par le service worker (public/sw.js).
     */
    public function toWebPush(object $notifiable): array
    {
        $data = $this->toArray($notifiable);

        return [
            'title' => $data['title'],
            'body' => $data['body'],
            'url' => $data['url'],
            'icon' => config('webpush.icon'),
            'badge' => config('webpush.badge'),
            'tag' => static::class,
        ];
    }
}
