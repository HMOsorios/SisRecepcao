<?php

namespace App\Services\Novosga;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Cliente da API REST do NovoSGA (motor de filas — Modelo 1, Seção 4.1/5.4).
 *
 * O SisRecepção NUNCA escreve direto nas tabelas do NovoSGA: toda a máquina de
 * estados de atendimento é orquestrada por este cliente via endpoints nativos.
 *
 * Autenticação: conta de serviço via OAuth2 (client_credentials) no endpoint
 * /api/token do NovoSGA. O atendente real é preservado via AtendimentoMeta
 * (namespace "sisrecepcao") — ver Seção 5.4, item 3.
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

    /**
     * Fábrica padrão, ligada ao config/novosga.php.
     */
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

    /**
     * Obtém (e cacheia) o token de acesso da conta de serviço.
     */
    public function token(): string
    {
        return Cache::remember('novosga_token', config('novosga.token_cache_ttl', 3600), function () {
            $body = [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ];

            if (empty($this->clientSecret) && $this->username !== '') {
                // Fallback para grant password (ambientes sem conta de serviço).
                $body = [
                    'grant_type' => 'password',
                    'client_id' => $this->clientId,
                    'username' => $this->username,
                    'password' => $this->password,
                ];
            }

            $response = $this->safe(fn () => $this->rawRequest()
                ->asForm()
                ->retry($this->retries, 300)
                ->post('/api/token', $body), '/api/token');

            $this->throwIfFailed($response, '/api/token');

            $json = $response->json();

            return (string) ($json['access_token'] ?? '');
        });
    }

    /**
     * POST /api/distribui — cria um Atendimento (emissão de senha/ticket).
     *
     * @param  array<string,mixed>|null  $cliente  (nome, documento, email, telefone, genero...)
     * @param  array<string,mixed>|null  $metadata  (namespace sisrecepcao — rastreabilidade)
     */
    public function distribuiSenha(
        int $unidade,
        int $servico,
        int $prioridade,
        ?array $cliente = null,
        ?array $metadata = null,
        ?int $agendamento = null,
    ): array {
        $payload = [
            'unidade' => $unidade,
            'servico' => $servico,
            'prioridade' => $prioridade,
            'cliente' => $cliente,
            'metadata' => $metadata,
            'agendamento' => $agendamento,
        ];

        // Sem retry automático: /api/distribui não é idempotente (cria um
        // Atendimento novo a cada chamada) e o NovoSGA não expõe chave de
        // idempotência — reenviar cegamente duplicaria a senha do cidadão.
        $response = $this->safe(fn () => $this->request()->post('/api/distribui', $payload), '/api/distribui');

        $this->throwIfFailed($response, '/api/distribui');

        return $response->json() ?? [];
    }

    /**
     * POST /api/atendimentos/{id}/chamar — chama a senha para um guichê/local.
     */
    public function chamar(int $atendimentoId, int $local, int $numeroLocal): array
    {
        $endpoint = "/api/atendimentos/{$atendimentoId}/chamar";

        // Sem retry automático (ação não idempotente — Seção 9.1/altitude review).
        $response = $this->safe(fn () => $this->request()->post($endpoint, [
            'local' => $local,
            'numeroLocal' => $numeroLocal,
        ]), $endpoint);

        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * POST /api/atendimentos/{id}/iniciar — marca o início do atendimento.
     */
    public function iniciar(int $atendimentoId): array
    {
        $endpoint = "/api/atendimentos/{$atendimentoId}/iniciar";

        $response = $this->safe(fn () => $this->request()->post($endpoint), $endpoint);

        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * POST /api/atendimentos/{id}/encerrar — finaliza, podendo redirecionar
     * para outro serviço/setor (redirecionamento interno, Seção 2.4).
     *
     * @param  array<int,int>  $servicosRealizados
     */
    public function encerrar(
        int $atendimentoId,
        array $servicosRealizados = [],
        ?int $servicoRedirecionado = null,
        ?int $novoUsuario = null,
    ): array {
        $endpoint = "/api/atendimentos/{$atendimentoId}/encerrar";

        $response = $this->safe(fn () => $this->request()->post($endpoint, [
            'servicosRealizados' => $servicosRealizados,
            'servicoRedirecionado' => $servicoRedirecionado,
            'novoUsuario' => $novoUsuario,
        ]), $endpoint);

        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * GET /api/atendimentos/{id} — detalhes de um atendimento (ticket emitido).
     */
    public function atendimento(int $id): array
    {
        $endpoint = "/api/atendimentos/{$id}";
        $response = $this->safe(fn () => $this->request()->get($endpoint), $endpoint);
        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * GET /api/filas/{unidadeId} — atendimentos na fila da unidade para o
     * usuário autenticado (console do atendente).
     */
    public function fila(int $unidadeId): array
    {
        $endpoint = "/api/filas/{$unidadeId}";
        $response = $this->safe(fn () => $this->request()->get($endpoint), $endpoint);
        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * PUT /api/filas — altera o status do atendimento atual do usuário.
     */
    public function alteraStatus(string $novoStatus): array
    {
        $response = $this->safe(fn () => $this->request()
            ->asForm()
            ->put('/api/filas', ['novoStatus' => $novoStatus]), '/api/filas');

        $this->throwIfFailed($response, '/api/filas');

        return $response->json() ?? [];
    }

    /**
     * GET /api/unidades/{id}/painel — últimas senhas chamadas (painel de TV).
     */
    public function painel(int $unidadeId, array $servicos = []): array
    {
        $endpoint = "/api/unidades/{$unidadeId}/painel";
        $response = $this->safe(fn () => $this->request()->get($endpoint, [
            'servicos' => implode(',', $servicos),
        ]), $endpoint);
        $this->throwIfFailed($response, $endpoint);

        return $response->json() ?? [];
    }

    /**
     * GET /api/unidades — catálogo de unidades de saúde.
     */
    public function unidades(): array
    {
        return $this->index('/api/unidades');
    }

    /**
     * GET /api/servicos — catálogo de serviços (filas).
     */
    public function servicos(): array
    {
        return $this->index('/api/servicos');
    }

    /**
     * GET /api/prioridades — prioridades legais configuráveis (idoso, gestante, PCD, TEA).
     */
    public function prioridades(): array
    {
        return $this->index('/api/prioridades');
    }

    /**
     * GET /api/departamentos — setores da unidade (Seção 2.6).
     */
    public function departamentos(): array
    {
        return $this->index('/api/departamentos');
    }

    /**
     * GET /api/locais — guichês/mesas de atendimento.
     */
    public function locais(): array
    {
        return $this->index('/api/locais');
    }

    /**
     * Health check simples — usado pelo fallback/painel para decidir entre
     * SSE + NovoSGA ou modo de contingência.
     */
    public function saudavel(): bool
    {
        try {
            $response = $this->request()->get('/api/unidades');

            return $response->successful();
        } catch (\Throwable) {
            // Qualquer falha (rede, token, resposta malformada) conta como
            // "não saudável" — este método alimenta decisões de fallback,
            // nunca deve propagar exceção (Seção 9.1).
            return false;
        }
    }

    /**
     * GET em endpoints de catálogo (paginados em "data" quando aplicável).
     */
    protected function index(string $endpoint): array
    {
        $response = $this->safe(fn () => $this->request()->get($endpoint), $endpoint);
        $this->throwIfFailed($response, $endpoint);

        $json = $response->json() ?? [];

        return $json['data'] ?? $json;
    }

    /**
     * Executa uma chamada HTTP ao NovoSGA tratando indisponibilidade de rede
     * (timeout, conexão recusada, DNS) da mesma forma que uma resposta 5xx —
     * ambas alimentam o mesmo fallback de contingência (Seção 9.1). Sem isso,
     * uma queda real do NovoSGA lança ConnectionException *antes* de existir
     * uma Response, e o outbox nunca é acionado.
     */
    protected function safe(\Closure $call, string $endpoint): Response
    {
        try {
            return $call();
        } catch (ConnectionException $e) {
            Log::error("[novosga] indisponível em {$endpoint}: {$e->getMessage()}");

            throw new NovosgaIndisponivelException($e->getMessage(), 0);
        }
    }

    /**
     * Requisição autenticada com o token da conta de serviço.
     */
    protected function request(): PendingRequest
    {
        return $this->rawRequest()->withToken($this->token());
    }

    /**
     * Requisição sem autenticação (usada na própria obtenção do token).
     */
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
            // Token expirado/revogado antes do TTL do cache: descarta para a
            // próxima chamada renovar, e trata como indisponibilidade — o
            // cidadão deve cair no fallback de contingência (Seção 9.1), não
            // ver um erro cru por até 1h (Seção 9.4).
            Cache::forget('novosga_token');
            Log::warning("[novosga] token rejeitado (401) em {$endpoint}, cache invalidado: {$message}");
            throw new NovosgaIndisponivelException($message, $response->status());
        }

        if ($response->status() >= 500 || $response->status() === 0) {
            Log::error("[novosga] indisponível em {$endpoint}: {$message}");
            throw new NovosgaIndisponivelException($message, $response->status());
        }

        throw new NovosgaApiException($message, $response->status());
    }
}
