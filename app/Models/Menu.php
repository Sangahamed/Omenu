<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id', 'name', 'description', 'category', 'price',
        'old_price', 'image', 'ingredients', 'allergens', 'calories',
        'preparation_time', 'is_available', 'popularity'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'allergens' => 'array',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }


}