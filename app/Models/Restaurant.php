<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;


class Restaurant extends Model
{
    use HasFactory,Searchable;

    protected $fillable = [
        'user_id', 'name', 'slug', 'description', 'logo', 'cover_image',
        'address', 'city', 'country', 'latitude', 'longitude', 'phone',
        'email', 'opening_hours', 'is_active', 'is_verified',
        'average_rating', 'total_orders', 'cuisine_type', 'price_range'
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
    ];

    /**
     * Genere un slug unique a partir d'un nom.
     *
     * La colonne slug est NOT NULL et unique : toute creation qui l'oublie
     * casse sur une contrainte SQL (c'etait le cas du formulaire admin).
     */
    public static function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name) ?: 'restaurant';
        $slug = $base;
        $i = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Restaurants visibles par le public : actifs ET valides par un admin.
     */
    public function scopePublished($query)
    {
        return $query->where('is_active', true)->where('is_verified', true);
    }

    /**
     * Restaurants en attente de validation par un administrateur.
     */
    public function scopePending($query)
    {
        return $query->where('is_verified', false);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favoritedBy($userId)
    {
        return $this->favorites()->where('user_id', $userId)->exists();
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'city' => $this->city,
            'cuisine_type' => $this->cuisine_type,
            'address' => $this->address,
        ];
    }
}
