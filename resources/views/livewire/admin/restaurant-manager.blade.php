<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-display font-semibold text-brand-black tracking-tight">Restaurants</h1>
            <p class="text-xs text-ink-soft uppercase tracking-widest font-semibold mt-1">
                Validation et gestion des établissements
            </p>
        </div>

        @if($pendingCount > 0)
            <button type="button" wire:click="setFilter('pending')"
                    class="flex items-center gap-2 bg-brand-red-soft border border-brand-red text-brand-red text-xs font-semibold px-4 py-2 rounded-sm">
                <i class="ri-time-line"></i>
                {{ $pendingCount }} en attente de validation
            </button>
        @endif
    </div>

    @if (session()->has('message'))
        <div class="flash flash-success mb-4">
            <div class="bg-white border border-brand-black text-ink text-sm rounded-sm px-4 py-3">
                {{ session('message') }}
            </div>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="flex flex-wrap items-center gap-2 mb-4">
        @foreach([
            'all' => 'Tous',
            'pending' => 'En attente',
            'verified' => 'Validés',
            'inactive' => 'Désactivés',
        ] as $key => $label)
            <button type="button" wire:click="setFilter('{{ $key }}')"
                    class="text-xs font-semibold px-3 py-1.5 rounded-sm border transition-colors
                           {{ $filter === $key
                              ? 'bg-brand-black text-white border-brand-black'
                              : 'bg-white text-ink-soft border-border hover:border-brand-black' }}">
                {{ $label }}
                @if($key === 'pending' && $pendingCount > 0)
                    <span class="ml-1 text-brand-red {{ $filter === $key ? 'text-white' : '' }}">({{ $pendingCount }})</span>
                @endif
            </button>
        @endforeach

        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un nom, une ville…"
               class="ml-auto w-64 border-border focus:border-brand-red focus:ring-brand-red rounded-sm text-sm px-3 py-1.5">
    </div>

    <form wire:submit.prevent="{{ $isEditing ? 'update' : 'store' }}" class="card bg-white border border-border rounded-md p-6 mb-6">
        <h2 class="font-display font-semibold text-ink mb-4">
            {{ $isEditing ? 'Modifier le restaurant' : 'Ajouter un restaurant' }}
        </h2>

        <div class="form-grid grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach([
                ['name', 'text', 'Nom'],
                ['city', 'text', 'Ville'],
                ['address', 'text', 'Adresse'],
                ['latitude', 'text', 'Latitude'],
                ['longitude', 'text', 'Longitude'],
                ['phone', 'text', 'Téléphone'],
                ['email', 'email', 'Email'],
                ['cuisine_type', 'text', 'Type de cuisine'],
                ['price_range', 'text', 'Gamme de prix'],
            ] as [$field, $type, $placeholder])
                <div>
                    <input type="{{ $type }}" wire:model="{{ $field }}" placeholder="{{ $placeholder }}"
                           class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2">
                    @error($field)
                        <p class="text-[11px] text-brand-red mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach

            <textarea wire:model="description" placeholder="Description"
                      class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm px-3 py-2 md:col-span-2"></textarea>

            <label class="checkbox md:col-span-2 flex items-center gap-2 text-sm text-ink-soft">
                <input type="checkbox" wire:model="is_active" value="1" class="rounded-sm border-border text-brand-red focus:ring-brand-red"> Actif
            </label>
        </div>

        <div class="flex items-center gap-2 mt-4">
            <button type="submit" class="bg-brand-black hover:bg-brand-black-2 text-white px-4 py-2 rounded-sm text-sm">
                {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
            </button>

            @if($isEditing)
                <button type="button" wire:click="resetInput" class="bg-white border border-border text-ink-soft px-4 py-2 rounded-sm text-sm">Annuler</button>
            @endif
        </div>
    </form>

    <div class="bg-white border border-border rounded-md overflow-x-auto">
        <table class="data-table w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-ink-soft border-b border-border">
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Propriétaire</th>
                    <th class="px-4 py-3">Ville</th>
                    <th class="px-4 py-3">Cuisine</th>
                    <th class="px-4 py-3">Validation</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($restaurants as $restaurant)
                    <tr class="{{ $restaurant->is_verified ? '' : 'bg-brand-red-soft/30' }}">
                        <td class="px-4 py-3 font-medium text-ink">{{ $restaurant->name }}</td>
                        <td class="px-4 py-3 text-ink-soft text-xs">
                            {{ $restaurant->user?->name ?? '—' }}<br>
                            <span class="text-[11px]">{{ $restaurant->user?->email }}</span>
                        </td>
                        <td class="px-4 py-3 text-ink-soft">{{ $restaurant->city }}</td>
                        <td class="px-4 py-3 text-ink-soft">{{ $restaurant->cuisine_type }}</td>
                        <td class="px-4 py-3">
                            @if($restaurant->is_verified)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#1F7A4D]">
                                    <i class="ri-verified-badge-line"></i> Valide
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-red">
                                    <i class="ri-time-line"></i> En attente
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge text-xs {{ $restaurant->is_active ? 'text-ink' : 'text-ink-soft' }}">
                                {{ $restaurant->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap items-center justify-end gap-1">
                                @if($restaurant->is_verified)
                                    <button wire:click="reject({{ $restaurant->id }})"
                                            class="bg-white border border-brand-red text-brand-red px-2 py-1 rounded-sm text-xs">
                                        Retirer la validation
                                    </button>
                                @else
                                    <button wire:click="approve({{ $restaurant->id }})"
                                            class="bg-brand-black hover:bg-brand-black-2 text-white px-2 py-1 rounded-sm text-xs">
                                        Valider
                                    </button>
                                @endif

                                <button wire:click="toggleActive({{ $restaurant->id }})"
                                        class="bg-white border border-border hover:border-brand-black text-ink-soft px-2 py-1 rounded-sm text-xs">
                                    {{ $restaurant->is_active ? 'Désactiver' : 'Activer' }}
                                </button>

                                <button wire:click="edit({{ $restaurant->id }})"
                                        class="bg-white border border-brand-black text-brand-black px-2 py-1 rounded-sm text-xs">
                                    Modifier
                                </button>

                                <button wire:click="delete({{ $restaurant->id }})"
                                        wire:confirm="Supprimer définitivement {{ $restaurant->name }} ?"
                                        class="bg-brand-red hover:bg-brand-red-hover text-white px-2 py-1 rounded-sm text-xs">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-ink-soft text-sm">
                            Aucun restaurant pour ce filtre.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination mt-4">
        {{ $restaurants->links() }}
    </div>
</div>
