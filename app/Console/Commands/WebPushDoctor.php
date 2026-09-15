<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;

/**
 * Diagnostic des notifications Web Push : dit precisement ce qui manque.
 */
class WebPushDoctor extends Command
{
    protected $signature = 'webpush:doctor';

    protected $description = 'Verifie la configuration des notifications Web Push';

    public function handle(): int
    {
        $ok = true;

        $this->newLine();
        $this->line('  <options=bold>Diagnostic Web Push</>');
        $this->newLine();

        // 1. Cles VAPID
        $hasKeys = filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key'));
        $this->status('Cles VAPID', $hasKeys, 'Lancez `php artisan webpush:vapid`.');
        $ok = $ok && $hasKeys;

        // 2. Cle publique exposee au front (Vite)
        $vitePath = base_path('.env');
        $hasVite = is_file($vitePath)
            && str_contains(file_get_contents($vitePath), 'VITE_VAPID_PUBLIC_KEY=');
        $this->status(
            'VITE_VAPID_PUBLIC_KEY dans .env',
            $hasVite,
            'Ajoutez VITE_VAPID_PUBLIC_KEY=<cle publique> puis relancez `npm run build`.'
        );
        $ok = $ok && $hasVite;

        // 3. Service worker
        $hasWorker = is_file(public_path('sw.js'));
        $this->status('Service worker public/sw.js', $hasWorker, 'Fichier manquant.');
        $ok = $ok && $hasWorker;

        // 4. OpenSSL : generation de cles ECDH ephemeres
        $canGenerate = $this->canGenerateEcKey();
        $this->status('OpenSSL (chiffrement du message)', $canGenerate, $this->opensslHint());
        $ok = $ok && $canGenerate;

        // 5. Abonnements enregistres
        $count = PushSubscription::count();
        $this->line(sprintf(
            '  %s  %-42s %s',
            $count > 0 ? '<fg=green>OK  </>' : '<fg=yellow>INFO</>',
            'Navigateurs abonnes',
            $count
        ));

        if ($count === 0) {
            $this->line('        Ouvrez le site, cliquez sur la cloche puis « Activer ».');
        }

        $this->newLine();
        $this->line($ok
            ? '  <fg=green>Configuration complete.</>'
            : '  <fg=yellow>Corrigez les points ci-dessus : la cloche et les toasts fonctionnent deja sans eux.</>');
        $this->newLine();

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    protected function status(string $label, bool $ok, string $hint): void
    {
        $this->line(sprintf('  %s  %s', $ok ? '<fg=green>OK  </>' : '<fg=red>KO  </>', $label));

        if (! $ok) {
            foreach (explode("\n", $hint) as $line) {
                $this->line('        '.$line);
            }
        }
    }

    protected function canGenerateEcKey(): bool
    {
        if (! extension_loaded('openssl')) {
            return false;
        }

        $key = @openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);

        while (openssl_error_string()) {
            // Vide la pile d'erreurs pour ne pas polluer les appels suivants.
        }

        return $key !== false;
    }

    protected function opensslHint(): string
    {
        $path = config('webpush.openssl_conf') ?: 'C:/xampp/apache/conf/openssl.cnf';

        if (! windows_os()) {
            return 'openssl ne peut pas generer de cle EC : verifiez l\'installation d\'OpenSSL.';
        }

        return "PHP lit OPENSSL_CONF au demarrage : posez la variable au niveau du systeme,\n"
            ."puis rouvrez le terminal (et redemarrez Apache) :\n"
            .'  setx OPENSSL_CONF "'.str_replace('/', '\\', $path).'"';
    }
}
