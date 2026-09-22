<?php

namespace Tests\Feature;

use App\Livewire\Contact;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_contact_est_accessible_sans_compte(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Contact');
    }

    public function test_le_formulaire_refuse_les_champs_invalides(): void
    {
        Livewire::test(Contact::class)
            ->set('name', 'A')
            ->set('email', 'pas-un-email')
            ->set('subject', 'x')
            ->set('message', 'court')
            ->call('submit')
            ->assertHasErrors(['name', 'email', 'subject', 'message']);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_un_message_valide_est_enregistre_et_notifie_les_administrateurs(): void
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        Livewire::test(Contact::class)
            ->set('name', 'Awa Traoré')
            ->set('email', 'awa@example.ci')
            ->set('subject', 'Problème sur ma commande')
            ->set('message', 'Ma commande est indiquée livrée mais je ne l\'ai jamais reçue.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true);

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'awa@example.ci',
            'subject' => 'Problème sur ma commande',
            'handled_at' => null,
        ]);

        Notification::assertSentTo($admin, ContactMessageReceived::class);
    }

    public function test_le_champ_piege_bloque_les_robots_sans_les_avertir(): void
    {
        Livewire::test(Contact::class)
            ->set('name', 'Robot')
            ->set('email', 'bot@spam.test')
            ->set('subject', 'Achetez maintenant')
            ->set('message', 'Message publicitaire non sollicité.')
            ->set('website', 'http://spam.test')
            ->call('submit')
            // Le robot croit avoir réussi, mais rien n'est enregistré.
            ->assertSet('sent', true);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_le_formulaire_est_prerempli_pour_un_utilisateur_connecte(): void
    {
        $user = User::factory()->create(['name' => 'Sanga', 'email' => 'sanga@example.ci']);

        Livewire::actingAs($user)
            ->test(Contact::class)
            ->assertSet('name', 'Sanga')
            ->assertSet('email', 'sanga@example.ci');
    }

    public function test_le_message_est_rattache_au_compte_de_son_auteur(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Contact::class)
            ->set('subject', 'Une question')
            ->set('message', 'Bonjour, je voudrais des précisions sur la livraison.')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('contact_messages', [
            'user_id' => $user->id,
        ]);
    }

    public function test_seuls_les_administrateurs_voient_les_messages(): void
    {
        $client = User::factory()->create();
        $client->syncRoles(['client']);

        $this->actingAs($client)->get('/admin/messages')->assertForbidden();

        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $this->actingAs($admin)->get('/admin/messages')->assertOk();
    }

    public function test_un_administrateur_marque_un_message_comme_traite(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles(['admin']);

        $message = ContactMessage::create([
            'name' => 'Koffi',
            'email' => 'koffi@example.ci',
            'subject' => 'Partenariat',
            'message' => 'Je souhaite inscrire mon restaurant sur la plateforme.',
        ]);

        Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\ContactMessages::class)
            ->call('markHandled', $message->id);

        $this->assertNotNull($message->fresh()->handled_at);
    }
}
