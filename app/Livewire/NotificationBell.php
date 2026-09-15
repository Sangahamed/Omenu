<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Cloche de notifications du header : derniers messages non lus + compteur.
 */
class NotificationBell extends Component
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = ! $this->open;
    }

    #[On('notificationsRefresh')]
    public function refreshNotifications(): void
    {
        // Le simple fait de rejouer render() suffit : les notifications sont
        // relues en base a chaque rendu.
    }

    public function markAsRead(string $id): void
    {
        $notification = Auth::user()?->notifications()->whereKey($id)->first();

        $notification?->markAsRead();
    }

    public function markAllAsRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();

        $this->dispatch('notify', type: 'success', message: 'Toutes les notifications ont été marquées comme lues.');
    }

    public function clearAll(): void
    {
        Auth::user()?->notifications()->delete();

        $this->open = false;
    }

    public function render()
    {
        $user = Auth::user();

        return view('livewire.notification-bell', [
            'notifications' => $user
                ? $user->notifications()->latest()->limit(12)->get()
                : collect(),
            'unreadCount' => $user ? $user->unreadNotifications()->count() : 0,
        ]);
    }
}
