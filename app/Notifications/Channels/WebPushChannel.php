<?php

namespace App\Notifications\Channels;

use App\Models\PushSubscription as PushSubscriptionModel;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Canal « webpush » : envoie la notification aux navigateurs abonnes.
 *
 * Le canal s'auto-desactive si les cles VAPID ne sont pas configurees, pour
 * que l'absence de configuration ne casse jamais l'envoi des autres canaux
 * (database, broadcast...).
 */
class WebPushChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (! method_exists($notification, 'toWebPush')) {
            return;
        }

        if (! $this->isConfigured()) {
            return;
        }

        $subscriptions = $notifiable->pushSubscriptions ?? null;

        if (! $subscriptions || $subscriptions->isEmpty()) {
            return;
        }

        $payload = json_encode($notification->toWebPush($notifiable), JSON_UNESCAPED_UNICODE);

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => config('webpush.vapid.subject'),
                    'publicKey' => config('webpush.vapid.public_key'),
                    'privateKey' => config('webpush.vapid.private_key'),
                ],
            ]);

            $webPush->setDefaultOptions([
                'TTL' => config('webpush.ttl'),
                'urgency' => config('webpush.urgency'),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Web Push indisponible : '.$e->getMessage());

            return;
        }

        /** @var PushSubscriptionModel $subscription */
        foreach ($subscriptions as $subscription) {
            try {
                $webPush->queueNotification(
                    Subscription::create([
                        'endpoint' => $subscription->endpoint,
                        'publicKey' => $subscription->public_key,
                        'authToken' => $subscription->auth_token,
                        'contentEncoding' => $subscription->content_encoding ?: 'aesgcm',
                    ]),
                    $payload
                );
            } catch (\Throwable $e) {
                Log::warning("Abonnement push #{$subscription->id} invalide : ".$e->getMessage());
            }
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getEndpoint();

            if ($report->isSuccess()) {
                PushSubscriptionModel::where('endpoint_hash', PushSubscriptionModel::hashEndpoint($endpoint))
                    ->update(['last_used_at' => now()]);

                continue;
            }

            // 404/410 : l'abonnement a ete revoque cote navigateur, on le purge
            // pour ne pas reessayer indefiniment.
            if ($report->isSubscriptionExpired()) {
                PushSubscriptionModel::where('endpoint_hash', PushSubscriptionModel::hashEndpoint($endpoint))->delete();

                continue;
            }

            Log::warning('Echec envoi Web Push : '.$report->getReason());
        }
    }

    protected function isConfigured(): bool
    {
        return filled(config('webpush.vapid.public_key'))
            && filled(config('webpush.vapid.private_key'));
    }
}
