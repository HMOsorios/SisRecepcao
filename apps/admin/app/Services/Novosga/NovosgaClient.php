<?php

namespace App\Services\Novosga;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente de LEITURA da API REST do NovoSGA para o painel administrativo
 * (BI e diretoria — Seção 2.5/8.1). Apenas GET; nenhuma escrita.
 */
class NovosgaClient
{
    public function __construct(
        protected readonly string $baseUrl,
        protected readonly string $clientId,
        protected readonly string $clientSecret,
        protected readonly string $username,
        protected readonly string $password,
        protected readonly int $timeout,
        protected readonly int $retries,
    ) {}

    public static function fromConfig(): self
    {
        return new self(
            baseUrl: rtrim((string) config('novosga.base_url'), '/'),
            clientId: (string) config('novosga.client_id'),
            clientSecret: (string) config('novosga.client_secret'),
            username: (string) config('novosga.username'),
            password: (string) config('novosga.password'),
            timeout: (int) config('novosga.timeout', 5),
            retries: (int) config('novosga.retries', 3),
        );
    }

    public function token(): string
    {
        return Cache::remember('novosga_token', config('novosga.token_cache_ttl', 3600), function () {
            $body = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];

            if (empty($this->clientSecret) && $this->username !== '') {
                $body = [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'username' => $this->username,
                    'password' => $this->password,
                ];
            }

            $response = $this->rawRequest()
                ->asForm()
                ->retry($this->retries, 300)
                ->post('/api/token', $body);

            $this->throwIfFailed($response, '/api/token');

            return (string) ($response->json()['access_token'] ?? '');
        });
    }

    /**
     * Atendimentos da API (GET /api/atendimentos) — para BI.
     *
     * @param  array<string,mixed>  $params  (q, sort, order, limit, offset)
     */
    public function atendimentos(array $params = []): array
    {
        return $this->index('/api/atendimentos', $params);
    }

    /**
     * Histórico de atendimentos (GET /api/atendimentoshistorico).
     *
     * @param  array<string,mixed>  $params
     */
    public function historico(array $params = []): array
    {
        return $this->index('/api/atendimentoshistorico', $params);
    }

    /**
     * Fila atual de uma unidade.
     */
    public function fila(int $unidadeId): array
    {
        return $this->index("/api/filas/{$unidadeId}");
    }

    public function unidades(): array
    {
        return $this->index('/api/unidades');
    }

    public function servicos(): array
    {
        return $this->index('/api/servicos');
    }

    public function departamentos(): array
    {
        return $this->index('/api/departamentos');
    }

    public function locais(): array
    {
        return $this->index('/api/locais');
    }

    public function saudavel(): bool
    {
        try {
            return $this->request()->get('/api/unidades')->successful();
        } catch (\Throwable) {
            return false;
        }
    }

    protected function index(string $endpoint, array $params = []): array
    {
        $response = $this->request()->get($endpoint, $params);
        $this->throwIfFailed($response, $endpoint);

        $json = $response->json() ?? [];

        return $json['data'] ?? $json;
    }

    protected function request(): PendingRequest
    {
        return $this->rawRequest()->withToken($this->token());
    }

    protected function rawRequest(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout($this->timeout);
    }

    protected function throwIfFailed(Response $response, string $endpoint): void
    {
        if ($response->successful()) {
            return;
        }

        $message = $response->json('error') ?? "NovoSGA respondeu HTTP {$response->status()} em {$endpoint}";

        if ($response->status() === 401) {
            Cache::forget('novosga_token');
            Log::warning("[admin-novosga] token rejeitado (401) em {$endpoint}, cache invalidado: {$message}");
            throw new NovosgaIndisponivelException($message, $response->status());
        }

        if ($response->status() >= 500 || $response->status() === 0) {
            Log::error("[admin-novosga] indisponível em {$endpoint}: {$message}");
            throw new NovosgaIndisponivelException($message, $response->status());
        }

        throw new NovosgaApiException($message, $response->status());
    }
}
