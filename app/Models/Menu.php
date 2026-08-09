<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;


class Menu extends Model
{
    use HasFactory,Searchable;

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

    public function toSearchableArray()
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurant_id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'price' => $this->price,
        ];
    }

    public function searchableAs()
    {
        return 'menus_index';
    }


}