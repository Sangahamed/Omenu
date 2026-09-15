<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Diagnostic du temps réel (Reverb + file d'attente).
 *
 * Le mode d'échec par défaut est silencieux : événement diffusé, aucune erreur,
 * et rien n'arrive au navigateur. Cette commande dit lequel des quatre maillons
 * est rompu.
 */
class RealtimeDoctor extends Command
{
    protected $signature = 'realtime:doctor';

    protected $description = 'Vérifie la chaîne du temps réel : diffusion, Reverb, file d\'attente, canaux';

    public function handle(): int
    {
        $ok = true;

        $this->newLine();
        $this->line('  <options=bold>Diagnostic temps réel</>');
        $this->newLine();

        // 1. Pilote de diffusion
        $driver = config('broadcasting.default');
        $driverOk = $driver === 'reverb';
        $this->status(
            "Pilote de diffusion : {$driver}",
            $driverOk,
            'Mettez BROADCAST_CONNECTION=reverb dans le .env (avec "log", tout part dans les journaux).'
        );
        $ok = $ok && $driverOk;

        // 2. Identifiants Reverb
        $keysOk = filled(config('broadcasting.connections.reverb.key'))
            && filled(config('broadcasting.connections.reverb.secret'))
            && filled(config('broadcasting.connections.reverb.app_id'));
        $this->status('Identifiants REVERB_APP_*', $keysOk, 'Renseignez REVERB_APP_ID / KEY / SECRET dans le .env.');
        $ok = $ok && $keysOk;

        // 3. Clé exposée au navigateur
        $env = is_file(base_path('.env')) ? file_get_contents(base_path('.env')) : '';
        $viteOk = str_contains($env, 'VITE_REVERB_APP_KEY=') && filled(config('broadcasting.connections.reverb.key'));
        $this->status(
            'VITE_REVERB_APP_KEY exposée au bundle',
            $viteOk,
            "Sans elle, resources/js/echo.js n'instancie pas window.Echo. Ajoutez-la puis relancez `npm run build`."
        );
        $ok = $ok && $viteOk;

        // 4. Serveur Reverb joignable
        $host = config('broadcasting.connections.reverb.options.host') ?: '127.0.0.1';
        $port = (int) (config('broadcasting.connections.reverb.options.port') ?: 8080);
        $reachable = $this->canConnect($host, $port);
        $this->status(
            "Serveur Reverb sur {$host}:{$port}",
            $reachable,
            'Démarrez-le : php artisan reverb:start'
        );
        $ok = $ok && $reachable;

        // 5. File d'attente : les événements ShouldBroadcast y transitent
        $pending = $this->pendingJobs();
        $failed = $this->failedJobs();

        $this->line(sprintf(
            '  %s  %s',
            $pending > 0 ? '<fg=yellow>ATTN</>' : '<fg=green>OK  </>',
            "File d'attente ({$pending} en attente, {$failed} en échec)"
        ));

        if ($pending > 0) {
            $this->line('        Des diffusions attendent un worker. Lancez : php artisan queue:work');
            $ok = false;
        }

        $this->newLine();
        $this->line('  Les événements implémentent ShouldBroadcast : ils passent par la file.');
        $this->line('  Un worker est donc indispensable, en plus du serveur Reverb.');
        $this->line('  <fg=gray>composer dev démarre serveur + worker + Reverb + Vite d\'un coup.</>');

        $this->newLine();
        $this->line($ok
            ? '  <fg=green>Chaîne complète : le temps réel est opérationnel.</>'
            : '  <fg=yellow>Corrigez les points ci-dessus pour que le navigateur reçoive les événements.</>');
        $this->newLine();

        return $ok ? self::SUCCESS : self::FAILURE;
    }

    protected function status(string $label, bool $ok, string $hint): void
    {
        $this->line(sprintf('  %s  %s', $ok ? '<fg=green>OK  </>' : '<fg=red>KO  </>', $label));

        if (! $ok) {
            $this->line('        '.$hint);
        }
    }

    protected function canConnect(string $host, int $port): bool
    {
        $socket = @fsockopen($host, $port, $errno, $errstr, 2);

        if ($socket === false) {
            return false;
        }

        fclose($socket);

        return true;
    }

    protected function pendingJobs(): int
    {
        return config('queue.default') === 'database'
            ? (int) DB::table('jobs')->count()
            : 0;
    }

    protected function failedJobs(): int
    {
        try {
            return (int) DB::table('failed_jobs')->count();
        } catch (\Throwable) {
            return 0;
        }
    }
}
