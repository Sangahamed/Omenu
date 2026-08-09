<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    ];

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
