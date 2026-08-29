<div>
    @if (session()->has('message'))
        <div class="flash flash-success">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="card bg-white border border-border rounded-md p-6 mb-6">
        <div class="form-grid grid grid-cols-2 gap-4">
            <input type="text" wire:model="name" placeholder="Nom" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="city" placeholder="Ville" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="address" placeholder="Adresse" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="latitude" placeholder="Latitude" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="longitude" placeholder="Longitude" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="phone" placeholder="Téléphone" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="email" wire:model="email" placeholder="Email" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="cuisine_type" placeholder="Type de cuisine" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <input type="text" wire:model="price_range" placeholder="Gamme de prix" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
            <textarea wire:model="description" placeholder="Description" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2 col-span-2"></textarea>
            <label class="checkbox col-span-2 flex items-center gap-2">
                <input type="checkbox" wire:model="is_active" value="1"> Actif
            </label>
        </div>
        <button type="submit" class="bg-brand-black hover:bg-brand-black-2 text-white px-4 py-2 mt-4 rounded-sm">
            {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
        </button>
        @if($isEditing)
            <button type="button" wire:click="resetInput" class="bg-white border border-border text-ink-soft px-4 py-2 mt-4 rounded-sm">Annuler</button>
        @endif
    </form>

    <table class="data-table w-full">
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
                <td><span class="badge {{ $restaurant->is_active ? 'badge-confirmee' : 'badge-annulee' }}">{{ $restaurant->is_active ? 'Actif' : 'Inactif' }}</span></td>
                <td>
                    <button wire:click="edit({{ $restaurant->id }})" class="bg-white border border-brand-black text-brand-black px-2 py-1 rounded-sm text-xs">Modifier</button>
                    <button wire:click="delete({{ $restaurant->id }})" class="bg-brand-red hover:bg-brand-red-hover text-white px-2 py-1 rounded-sm text-xs" onclick="confirm('Supprimer ?') || event.stopImmediatePropagation()">Supprimer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination mt-4">
        {{ $restaurants->links() }}
    </div>
</div>
