<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

/**
 * Genere la paire de cles VAPID necessaire au Web Push et l'ecrit dans le .env.
 */
class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid {--show : Affiche les cles sans ecrire dans le .env}';

    protected $description = 'Genere les cles VAPID utilisees par les notifications Web Push';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->newLine();
        $this->line('  <fg=green>VAPID_PUBLIC_KEY</>='.$keys['publicKey']);
        $this->line('  <fg=green>VAPID_PRIVATE_KEY</>='.$keys['privateKey']);
        $this->newLine();

        if ($this->option('show')) {
            return self::SUCCESS;
        }

        $path = base_path('.env');

        if (! is_writable($path)) {
            $this->warn('Le fichier .env n\'est pas accessible en ecriture : copiez les cles ci-dessus manuellement.');

            return self::SUCCESS;
        }

        $env = file_get_contents($path);

        foreach ([
            'VAPID_SUBJECT' => config('app.url'),
            'VAPID_PUBLIC_KEY' => $keys['publicKey'],
            'VAPID_PRIVATE_KEY' => $keys['privateKey'],
            'VITE_VAPID_PUBLIC_KEY' => '${VAPID_PUBLIC_KEY}',
        ] as $key => $value) {
            $line = $key.'='.$value;

            $env = preg_match("/^{$key}=.*$/m", $env)
                ? preg_replace("/^{$key}=.*$/m", $line, $env)
                : rtrim($env, "\r\n")."\n".$line."\n";
        }

        file_put_contents($path, $env);

        $this->info('Cles ecrites dans .env. Lancez `npm run build` puis `php artisan config:clear`.');

        return self::SUCCESS;
    }
}
