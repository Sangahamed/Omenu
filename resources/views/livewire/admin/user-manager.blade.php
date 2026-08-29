<div class="card bg-white rounded-md border border-border p-6">
    <h2 class="text-xl font-display font-semibold text-ink mb-4">Gestion des utilisateurs</h2>

    <input type="text" wire:model.live="search" placeholder="Rechercher..." class="w-full border-border focus:border-brand-red focus:ring-brand-red rounded-sm mb-4">

    <table class="data-table w-full">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    <select wire:change="updateRole({{ $user->id }}, $event.target.value)" class="border-border focus:border-brand-red focus:ring-brand-red rounded-sm text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <button wire:click="toggleBan({{ $user->id }})" class="{{ $user->is_banned ? 'bg-brand-black' : 'bg-white border border-brand-black text-brand-black' }} {{ $user->is_banned ? 'text-white' : '' }} px-2 py-1 rounded-sm text-xs">
                        {{ $user->is_banned ? 'Débannir' : 'Bannir' }}
                    </button>
                    <button wire:click="deleteUser({{ $user->id }})" class="bg-brand-red hover:bg-brand-red-hover text-white px-2 py-1 rounded-sm text-xs" onclick="confirm('Supprimer ?') || event.stopImmediatePropagation()">Supprimer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination mt-4">
        {{ $users->links() }}
    </div>
</div>
