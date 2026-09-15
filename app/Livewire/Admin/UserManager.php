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

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $users = User::query()
            ->with('roles')
            ->when($this->search !== '', function ($query) {
                // Le groupement est indispensable : sans lui, le orWhere
                // s'echapperait de tout futur filtre ajoute a la requete.
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->latest()
            ->paginate(15);

        return view('livewire.admin.user-manager', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ])->layout('layouts.app');
    }

    public function updateRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);
        $user->syncRoles([$role->name]);

        $this->dispatch('notify', type: 'success', message: "Rôle de {$user->name} mis à jour : {$role->name}.");
    }

    public function toggleBan($userId)
    {
        $user = User::findOrFail($userId);

        if ($user->id === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Vous ne pouvez pas bannir votre propre compte.');

            return;
        }

        $user->is_banned = ! $user->is_banned;
        $user->save();

        $this->dispatch(
            'notify',
            type: $user->is_banned ? 'warning' : 'success',
            message: $user->is_banned ? "{$user->name} a été banni." : "{$user->name} a été réactivé."
        );
    }

    public function deleteUser($userId)
    {
        if ((int) $userId === auth()->id()) {
            $this->dispatch('notify', type: 'error', message: 'Vous ne pouvez pas supprimer votre propre compte.');

            return;
        }

        User::findOrFail($userId)->delete();

        $this->dispatch('notify', type: 'success', message: 'Utilisateur supprimé.');
    }
}
