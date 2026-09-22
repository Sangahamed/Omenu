<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Support\Str;

/**
 * Prévient les administrateurs qu'un message de contact est arrivé.
 */
class ContactMessageReceived extends AppNotification
{
    public function __construct(public ContactMessage $contactMessage)
    {
    }

    public function payload(object $notifiable): array
    {
        return [
            'title' => 'Message : '.$this->contactMessage->subject,
            'body' => $this->contactMessage->name.' — '.Str::limit($this->contactMessage->message, 80),
            'url' => route('admin.messages'),
            'icon' => 'ri-mail-line',
            'level' => 'info',
            'contact_message_id' => $this->contactMessage->id,
        ];
    }
}
