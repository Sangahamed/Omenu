<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function show($slug)
    {
        $restaurant = Restaurant::with('menus')->where('slug', $slug)->firstOrFail();
        return view('restaurant.show', compact('restaurant'));
    }
}