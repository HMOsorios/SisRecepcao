<?php

namespace App\Services\Fila;

use App\Services\Novosga\NovosgaClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Catálogo de serviços/prioridades do totem (Módulo 2.1).
 *
 * Consome o NovoSGA via API com cache; em contingência usa o catálogo de
 * fallback do config/totem.php para o totem nunca ficar vazio (Seção 9.1).
 */
class CatalogoService
{
    public function __construct(protected readonly NovosgaClient $client) {}

    public function servicos(): array
    {
        return $this->comFallback('servicos', function () {
            return array_map(fn (array $s) => [
                'id' => (int) $s['id'],
                'nome' => (string) $s['nome'],
                'sigla' => (string) ($s['sigla'] ?? ''),
            ], $this->client->servicos());
        }, config('totem.services'));
    }

    public function prioridades(): array
    {
        return $this->comFallback('prioridades', function () {
            return array_map(fn (array $p) => [
                'id' => (int) $p['id'],
                'nome' => (string) $p['nome'],
                'peso' => (int) ($p['peso'] ?? 0),
                'cor' => (string) ($p['cor'] ?? '#1f6fb2'),
            ], $this->client->prioridades());
        }, config('totem.priorities'));
    }

    public function departamentos(): array
    {
        return $this->comFallback('departamentos', function () {
            return array_map(fn (array $d) => [
                'id' => (int) $d['id'],
                'nome' => (string) $d['nome'],
            ], $this->client->departamentos());
        }, []);
    }

    public function locais(): array
    {
        return $this->comFallback('locais', function () {
            return array_map(fn (array $l) => [
                'id' => (int) $l['id'],
                'nome' => (string) $l['nome'],
            ], $this->client->locais());
        }, []);
    }

    public function unidadeId(): int
    {
        return (int) config('sms.unidade_id', 1);
    }

    /**
     * Busca com cache de 5min; em falha usa fallback.
     *
     * @template T
     *
     * @param  callable(): T  $busca
     * @param  T  $fallback
     * @return T
     */
    protected function comFallback(string $chave, callable $busca, mixed $fallback): mixed
    {
        try {
            return Cache::remember("catalogo_{$chave}", 300, $busca);
        } catch (\Throwable $e) {
            Log::warning("[totem] catálogo {$chave} indisponível, usando fallback: {$e->getMessage()}");

            return $fallback;
        }
    }
}
