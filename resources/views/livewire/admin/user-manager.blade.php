<div class="bg-white rounded-xl shadow-md p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Gestion des utilisateurs</h2>

    <input type="text" wire:model.live="search" placeholder="Rechercher..." class="w-full border-gray-300 rounded-lg mb-4">

    <table class="w-full">
        <thead>
            <tr class="border-b">
                <th class="text-left py-2">Nom</th>
                <th class="text-left py-2">Email</th>
                <th class="text-left py-2">Rôle</th>
                <th class="text-left py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr class="border-b">
                <td class="py-2">{{ $user->name }}</td>
                <td class="py-2">{{ $user->email }}</td>
                <td class="py-2">
                    <select wire:change="updateRole({{ $user->id }}, $event.target.value)" class="border-gray-300 rounded text-sm">
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </td>
                <td class="py-2">
                    <button wire:click="toggleBan({{ $user->id }})" class="{{ $user->is_banned ? 'bg-green-600' : 'bg-yellow-600' }} text-white px-2 py-1 rounded text-xs">
                        {{ $user->is_banned ? 'Débannir' : 'Bannir' }}
                    </button>
                    <button wire:click="deleteUser({{ $user->id }})" class="bg-red-600 text-white px-2 py-1 rounded text-xs" onclick="confirm('Supprimer ?') || event.stopImmediatePropagation()">Supprimer</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $users->links() }}
</div>