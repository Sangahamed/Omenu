<?php

namespace App\Livewire\Admin;

use App\Models\ContactMessage;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Consultation des messages reçus via le formulaire de contact.
 */
class ContactMessages extends Component
{
    use WithPagination;

    public const FILTERS = [
        'pending' => 'À traiter',
        'handled' => 'Traités',
        'all' => 'Tous',
    ];

    #[Url(as: 'filter', except: 'pending')]
    public string $filter = 'pending';

    public ?int $openId = null;

    public function setFilter(string $filter): void
    {
        $this->filter = array_key_exists($filter, self::FILTERS) ? $filter : 'pending';
        $this->resetPage();
    }

    public function toggle(int $id): void
    {
        $this->openId = $this->openId === $id ? null : $id;
    }

    public function markHandled(int $id): void
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['handled_at' => $message->handled_at ? null : now()]);

        $this->dispatch(
            'notify',
            type: 'success',
            message: $message->handled_at ? 'Message marqué comme traité.' : 'Message remis à traiter.'
        );
    }

    public function delete(int $id): void
    {
        ContactMessage::findOrFail($id)->delete();

        $this->dispatch('notify', type: 'success', message: 'Message supprimé.');
    }

    public function render()
    {
        $messages = ContactMessage::query()
            ->with('user')
            ->when($this->filter === 'pending', fn ($q) => $q->whereNull('handled_at'))
            ->when($this->filter === 'handled', fn ($q) => $q->whereNotNull('handled_at'))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.contact-messages', [
            'messages' => $messages,
            'filters' => self::FILTERS,
            'pendingCount' => ContactMessage::whereNull('handled_at')->count(),
        ])->layout('layouts.app');
    }
}
