<?php

namespace App\Livewire;

use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;

/**
 * Formulaire de contact public.
 */
class Contact extends Component
{
    #[Validate('required|string|min:2|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|min:3|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10|max:5000')]
    public string $message = '';

    /**
     * Champ piège : invisible pour un humain, rempli par la plupart des robots.
     */
    public string $website = '';

    public bool $sent = false;

    public function mount(): void
    {
        if ($user = Auth::user()) {
            $this->name = $user->name;
            $this->email = $user->email;
        }
    }

    public function submit(): void
    {
        if ($this->website !== '') {
            // On simule un envoi réussi : inutile d'indiquer au robot qu'il a été repéré.
            $this->sent = true;

            return;
        }

        $this->validate();

        // Cinq messages par heure et par IP : sans limite, le formulaire est
        // une porte ouverte au spam.
        $key = 'contact:'.request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $this->dispatch('notify', type: 'error', message: 'Trop de messages envoyés. Réessayez dans un moment.');

            return;
        }

        RateLimiter::hit($key, 3600);

        $contactMessage = ContactMessage::create([
            'user_id' => Auth::id(),
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
            'ip_address' => request()->ip(),
        ]);

        try {
            $admins = User::role(['super-admin', 'admin'])->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ContactMessageReceived($contactMessage));
            }
        } catch (\Throwable $e) {
            // Le message est enregistré : une notification en échec ne doit pas
            // faire croire à l'expéditeur que son envoi a échoué.
            Log::error('Notification de message de contact impossible : '.$e->getMessage());
        }

        $this->sent = true;
        $this->reset(['subject', 'message']);

        $this->dispatch('notify', type: 'success', message: 'Votre message a bien été envoyé.');
    }

    public function sendAnother(): void
    {
        $this->sent = false;
    }

    public function render()
    {
        return view('livewire.contact')->extends('components.front.layouts.front');
    }
}
