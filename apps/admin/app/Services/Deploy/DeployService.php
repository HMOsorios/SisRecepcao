<?php

namespace App\Services\Deploy;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

/**
 * Ações do Painel de Configurações e Manutenção (Developer — Seção 8.4):
 * pull do GitHub, migrações, cache de configuração e deploy via Coolify.
 *
 * Todas as ações são registradas na trilha de auditoria pelo controller.
 */
class DeployService
{
    /**
     * `git pull` no repositório da aplicação.
     *
     * @return array{ok: bool, saida: string}
     */
    public function gitPull(string $baseDir): array
    {
        return $this->executar(['git', 'pull', '--ff-only'], $baseDir);
    }

    /**
     * Executa as migrações do banco.
     *
     * @return array{ok: bool, saida: string}
     */
    public function migrar(string $baseDir): array
    {
        return $this->executar([PHP_BINARY, 'artisan', 'migrate', '--force'], $baseDir);
    }

    /**
     * Recompila os caches de config/route/view.
     *
     * @return array{ok: bool, saida: string}
     */
    public function recarregarCaches(string $baseDir): array
    {
        $saida = [];
        foreach (['config:cache', 'route:cache', 'view:cache'] as $cmd) {
            $res = $this->executar([PHP_BINARY, 'artisan', $cmd], $baseDir);
            $saida[] = $res['saida'];
            if (! $res['ok']) {
                return ['ok' => false, 'saida' => implode(PHP_EOL, $saida)];
            }
        }

        return ['ok' => true, 'saida' => implode(PHP_EOL, $saida)];
    }

    /**
     * Dispara o deploy no Coolify (webhook) — Seção 8.4/8.9.
     *
     * @return array{ok: bool, saida: string}
     */
    public function deployCoolify(): array
    {
        $url = (string) config('sms.coolify_webhook_url');

        if ($url === '') {
            return ['ok' => false, 'saida' => 'Webhook do Coolify não configurado (COOLIFY_WEBHOOK_URL).'];
        }

        try {
            $response = Http::timeout(15)->post($url);

            return ['ok' => $response->successful(), 'saida' => "Coolify respondeu HTTP {$response->status()}."];
        } catch (\Throwable $e) {
            Log::error('[admin-deploy] falha no webhook do Coolify: '.$e->getMessage());

            return ['ok' => false, 'saida' => 'Falha ao acionar o Coolify: '.$e->getMessage()];
        }
    }

    /**
     * @param  array<int,string>  $comando
     * @return array{ok: bool, saida: string}
     */
    protected function executar(array $comando, string $baseDir): array
    {
        // Lock: git pull/migrate/cache concorrentes no mesmo working tree
        // podem colidir (índice do git, migração parcial). Uma segunda ação
        // disparada enquanto a primeira roda espera em vez de correr em
        // paralelo (Seção 9.4).
        $lock = Cache::lock('admin-deploy-'.basename($baseDir), 120);

        return $lock->block(10, function () use ($comando, $baseDir) {
            $process = new Process($comando, $baseDir, timeout: 120);

            try {
                $process->mustRun();
                $ok = true;
                $saida = trim($process->getOutput().$process->getErrorOutput());
            } catch (ProcessFailedException $e) {
                $ok = false;
                $saida = $e->getMessage();
            }

            Log::info('[admin-deploy] '.implode(' ', $comando).' => '.($ok ? 'ok' : 'falhou'));

            return ['ok' => $ok, 'saida' => $saida];
        });
    }
}
