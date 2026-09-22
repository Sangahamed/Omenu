<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Message envoyé depuis le formulaire de contact public.
 */
class ContactMessage extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'subject', 'message', 'ip_address', 'handled_at',
    ];

    protected $casts = [
        'handled_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('handled_at');
    }

    public function isHandled(): bool
    {
        return $this->handled_at !== null;
    }
}
