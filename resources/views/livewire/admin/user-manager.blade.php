<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <h1 class="text-2xl font-display font-semibold text-brand-black tracking-tight">Utilisateurs</h1>
        <p class="text-xs text-ink-soft uppercase tracking-widest font-semibold mt-1">
            Rôles, accès et modération des comptes
        </p>
    </div>

    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher par nom ou email..."
           class="w-full md:w-96 border-border focus:border-brand-red focus:ring-brand-red rounded-sm mb-4 px-3 py-2 text-sm">

    <div class="bg-white border border-border rounded-md overflow-x-auto">
        <table class="data-table w-full text-sm">
            <thead>
                <tr class="text-left text-xs uppercase tracking-wider text-ink-soft border-b border-border">
                    <th class="px-4 py-3">Nom</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Rôle</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-border">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3 font-medium text-ink">{{ $user->name }}</td>
                        <td class="px-4 py-3 text-ink-soft">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <select wire:change="updateRole({{ $user->id }}, $event.target.value)"
                                    class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm text-xs py-1">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" @selected($user->hasRole($role->name))>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            @if($user->is_banned)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-red">
                                    <i class="ri-forbid-line"></i> Banni
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold text-[#1F7A4D]">
                                    <i class="ri-checkbox-circle-line"></i> Actif
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="toggleBan({{ $user->id }})"
                                        class="px-2 py-1 rounded-sm text-xs border {{ $user->is_banned ? 'bg-brand-black text-white border-brand-black' : 'bg-white border-brand-black text-brand-black' }}">
                                    {{ $user->is_banned ? 'Débannir' : 'Bannir' }}
                                </button>

                                <button wire:click="deleteUser({{ $user->id }})"
                                        wire:confirm="Supprimer définitivement {{ $user->name }} ?"
                                        class="bg-brand-red hover:bg-brand-red-hover text-white px-2 py-1 rounded-sm text-xs">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center text-ink-soft text-sm">
                            Aucun utilisateur ne correspond à cette recherche.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination mt-4">
        {{ $users->links() }}
    </div>
</div>
