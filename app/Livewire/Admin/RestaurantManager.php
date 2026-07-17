<?php

namespace App\Livewire\Admin;

use App\Models\Restaurant;
use Livewire\Component;
use Livewire\WithPagination;

class RestaurantManager extends Component
{
    use WithPagination;

    public $name, $description, $address, $city, $latitude, $longitude, $phone, $email, $cuisine_type, $price_range, $is_active;
    public $restaurantId;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'address' => 'required|string',
        'city' => 'required|string',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'phone' => 'nullable|string',
        'email' => 'nullable|email',
        'cuisine_type' => 'nullable|string',
        'price_range' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $restaurants = Restaurant::paginate(10);
        return view('livewire.admin.restaurant-manager', compact('restaurants'));
    }

    public function resetInput()
    {
        $this->name = '';
        $this->description = '';
        $this->address = '';
        $this->city = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->phone = '';
        $this->email = '';
        $this->cuisine_type = '';
        $this->price_range = '';
        $this->is_active = true;
        $this->restaurantId = null;
        $this->isEditing = false;
    }

    public function store()
    {
        $this->validate();
        Restaurant::create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'cuisine_type' => $this->cuisine_type,
            'price_range' => $this->price_range,
            'is_active' => $this->is_active ?? true,
        ]);
        session()->flash('message', 'Restaurant créé avec succès.');
        $this->resetInput();
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->restaurantId = $id;
        $this->name = $restaurant->name;
        $this->description = $restaurant->description;
        $this->address = $restaurant->address;
        $this->city = $restaurant->city;
        $this->latitude = $restaurant->latitude;
        $this->longitude = $restaurant->longitude;
        $this->phone = $restaurant->phone;
        $this->email = $restaurant->email;
        $this->cuisine_type = $restaurant->cuisine_type;
        $this->price_range = $restaurant->price_range;
        $this->is_active = $restaurant->is_active;
        $this->isEditing = true;
    }

    public function update()
    {
        $this->validate();
        $restaurant = Restaurant::findOrFail($this->restaurantId);
        $restaurant->update([
            'name' => $this->name,
            'description' => $this->description,
            'address' => $this->address,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'cuisine_type' => $this->cuisine_type,
            'price_range' => $this->price_range,
            'is_active' => $this->is_active,
        ]);
        session()->flash('message', 'Restaurant mis à jour.');
        $this->resetInput();
    }

    public function delete($id)
    {
        Restaurant::findOrFail($id)->delete();
        session()->flash('message', 'Restaurant supprimé.');
    }
}