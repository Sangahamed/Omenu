<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'restaurant_id', 'delivery_person_id', 'parent_id',
        'subtotal', 'delivery_fee', 'tax', 'discount', 'total',
        'status', 'payment_method', 'payment_id', 'payment_status',
        'delivery_address', 'delivery_instructions', 'customer_phone', 'customer_name',
        'estimated_delivery_time', 'notes',
        // Horodatages du cycle de vie : sans eux dans le fillable, les
        // `Order::create([...])` et `->update([...])` les perdaient en silence
        // (la prise en charge par un livreur n'était jamais enregistrée, et
        // les commandes multi-restaurants perdaient leur parent_id).
        'accepted_at', 'preparing_at', 'ready_at', 'picked_up_at',
        'delivered_at', 'cancelled_at',
    ];

    protected $casts = [
        'estimated_delivery_time' => 'datetime',
        'accepted_at' => 'datetime',
        'preparing_at' => 'datetime',
        'ready_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'delivered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function deliveryPerson(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Statuts helpers
    public function isPending(): bool { return $this->status === 'pending'; }
    public function isAccepted(): bool { return $this->status === 'accepted'; }
    public function isPreparing(): bool { return $this->status === 'preparing'; }
    public function isReady(): bool { return $this->status === 'ready'; }
    public function isPickedUp(): bool { return $this->status === 'picked_up'; }
    public function isDelivered(): bool { return $this->status === 'delivered'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'En attente',
            'accepted' => 'Acceptée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'picked_up' => 'Récupérée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute(): string
    {
        // Classes alignées sur le design system de production (.badge-*)
        return [
            'pending' => 'badge-en_attente',
            'accepted' => 'badge-confirmee',
            'preparing' => 'badge-confirmee',
            'ready' => 'badge-honoree',
            'picked_up' => 'badge-honoree',
            'delivered' => 'badge-honoree',
            'cancelled' => 'badge-annulee',
        ][$this->status] ?? 'badge-en_attente';
    }
}