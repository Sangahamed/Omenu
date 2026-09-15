<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Abonnement Web Push d'un navigateur (PushSubscription cote JS).
 */
class PushSubscription extends Model
{
    protected $fillable = [
        'user_id', 'endpoint', 'endpoint_hash', 'public_key',
        'auth_token', 'content_encoding', 'user_agent', 'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function hashEndpoint(string $endpoint): string
    {
        return hash('sha256', $endpoint);
    }
}
