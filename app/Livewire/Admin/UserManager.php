<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class UserManager extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedUser;
    public $selectedRole;

    public function render()
    {
        $users = User::where('name', 'like', "%{$this->search}%")
            ->orWhere('email', 'like', "%{$this->search}%")
            ->paginate(15);

        $roles = Role::all();

        return view('livewire.admin.user-manager', compact('users', 'roles'));
    }

    public function updateRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->syncRoles([$role->name]);
        $this->dispatch('notify', message: 'Rôle mis à jour !');
    }

    public function toggleBan($userId)
    {
        $user = User::findOrFail($userId);
        $user->is_banned = !$user->is_banned;
        $user->save();
        $this->dispatch('notify', message: $user->is_banned ? 'Utilisateur banni' : 'Utilisateur réactivé');
    }

    public function deleteUser($userId)
    {
        User::findOrFail($userId)->delete();
        $this->dispatch('notify', message: 'Utilisateur supprimé');
    }
}