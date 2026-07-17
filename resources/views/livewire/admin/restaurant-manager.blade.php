<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="mb-6">
        <div class="grid grid-cols-2 gap-4">
            <input type="text" wire:model="name" placeholder="Nom" class="border p-2">
            <input type="text" wire:model="city" placeholder="Ville" class="border p-2">
            <input type="text" wire:model="address" placeholder="Adresse" class="border p-2">
            <input type="text" wire:model="latitude" placeholder="Latitude" class="border p-2">
            <input type="text" wire:model="longitude" placeholder="Longitude" class="border p-2">
            <input type="text" wire:model="phone" placeholder="Téléphone" class="border p-2">
            <input type="email" wire:model="email" placeholder="Email" class="border p-2">
            <input type="text" wire:model="cuisine_type" placeholder="Type de cuisine" class="border p-2">
            <input type="text" wire:model="price_range" placeholder="Gamme de prix" class="border p-2">
            <textarea wire:model="description" placeholder="Description" class="border p-2 col-span-2"></textarea>
            <label class="col-span-2">
                <input type="checkbox" wire:model="is_active" value="1"> Actif
            </label>
        </div>
        <button type="submit" class="bg-blue-500 text-white px-4 py-2 mt-2">
            {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
        </button>
        @if($isEditing)
            <button type="button" wire:click="resetInput" class="bg-gray-400 text-white px-4 py-2 mt-2">Annuler</button>
        @endif
    </form>

    <table class="w-full border">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Ville</th>
                <th>Cuisine</th>
                <th>Prix</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($restaurants as $restaurant)
            <tr>
                <td>{{ $restaurant->name }}</td>
                <td>{{ $restaurant->city }}</td>
                <td>{{ $restaurant->cuisine_type }}</td>
                <td>{{ $restaurant->price_range }}</td>
                <td>{{ $restaurant->is_active ? 'Actif' : 'Inactif' }}</td>
                <td>
                    <button wire:click="edit({{ $restaurant->id }})" class="bg-yellow-500 text-white px-2 py-1">Modifier</button>
                    <button wire:click="delete({{ $restaurant->id }})" class="bg-red-500 text-white px-2 py-1" onclick="confirm('Supprimer ?') || event.stopImmediatePropagation()">Supprimer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $restaurants->links() }}
</div>