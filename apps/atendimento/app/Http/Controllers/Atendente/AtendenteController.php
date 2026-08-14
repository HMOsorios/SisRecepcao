<?php

namespace App\Http\Controllers\Atendente;

use App\Http\Controllers\Controller;
use App\Services\Fila\CatalogoService;
use App\Services\Fila\ConsoleFilaService;
use App\Services\Keycloak\KeycloakClient;
use App\Services\Notificacoes\NotificacaoService;
use App\Services\Novosga\MercureStream;
use App\Services\Novosga\NovosgaApiException;
use App\Services\Novosga\NovosgaClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Módulo 2.4 — Painel do atendente/console do setor.
 *
 * A fila vem do NovoSGA (GET /api/filas/{unidade}); as chamadas disparam
 * /chamar → /iniciar, que publicam notificaPainel no Mercure e atualizam o
 * telão automaticamente (Seção 2.6, passos 3–4).
 */
class AtendenteController extends Controller
{
    public function __construct(
        protected readonly NovosgaClient $client,
        protected readonly ConsoleFilaService $filaService,
        protected readonly CatalogoService $catalogo,
        protected readonly NotificacaoService $notificacoes,
        protected readonly MercureStream $mercure,
        protected readonly KeycloakClient $keycloak,
    ) {}

    public function index(): View
    {
        $unidadeId = $this->catalogo->unidadeId();
        $usuario = $this->keycloak->usuario() ?? [];

        return view('atendente.console', [
            'unidadeId' => $unidadeId,
            'usuarioNome' => $usuario['name'] ?? $usuario['preferred_username'] ?? 'Atendente',
            'locais' => $this->catalogo->locais(),
            'servicos' => $this->catalogo->servicos(),
            'departamentoId' => $usuario['departamento_id'] ?? null,
            'filaUrl' => route('atendente.fila'),
            'mercureUrl' => $this->mercure->eventSourceUrl([
                $this->mercure->topicFilaUnidade($unidadeId),
            ]),
            'pollInterval' => config('mercure.poll_interval'),
        ]);
    }

    /**
     * Fila de atendimentos da unidade (para o usuário da conta de serviço).
     */
    public function fila(): JsonResponse
    {
        return $this->comFallbackNovosga(function () {
            $fila = $this->client->fila($this->catalogo->unidadeId());

            return response()->json([
                'fila' => $fila,
                'proxima' => $this->filaService->proximaSenha($fila),
            ]);
        });
    }

    /**
     * Botão "próxima senha" — escolhe e chama (prioridade legal primeiro).
     */
    public function proxima(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'local' => ['required', 'integer', 'min:1'],
            'numeroLocal' => ['required', 'integer', 'min:0'],
        ]);

        return $this->comFallbackNovosga(function () use ($validated) {
            $fila = $this->client->fila($this->catalogo->unidadeId());
            $ticket = $this->filaService->proximaSenha($fila);

            if ($ticket === null) {
                return response()->json(['message' => 'Nenhuma senha na fila.'], 404);
            }

            $atendimento = $this->client->chamar(
                (int) $ticket['id'],
                (int) $validated['local'],
                (int) $validated['numeroLocal'],
            );

            return response()->json(['atendimento' => $atendimento]);
        });
    }

    /**
     * Chamar uma senha específica para o guichê.
     */
    public function chamar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id' => ['required', 'integer', 'min:1'],
            'local' => ['required', 'integer', 'min:1'],
            'numeroLocal' => ['required', 'integer', 'min:0'],
        ]);

        return $this->comFallbackNovosga(function () use ($validated) {
            $atendimento = $this->client->chamar(
                (int) $validated['id'],
                (int) $validated['local'],
                (int) $validated['numeroLocal'],
            );

            return response()->json(['atendimento' => $atendimento]);
        });
    }

    public function iniciar(int $id): JsonResponse
    {
        return $this->comFallbackNovosga(function () use ($id) {
            $atendimento = $this->client->iniciar($id);

            return response()->json(['atendimento' => $atendimento]);
        });
    }

    public function encerrar(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'servicosRealizados' => ['nullable', 'array'],
            'servicosRealizados.*' => ['integer'],
            'servicoRedirecionado' => ['nullable', 'integer'],
            'novoUsuario' => ['nullable', 'integer'],
        ]);

        return $this->comFallbackNovosga(function () use ($id, $validated) {
            $atendimento = $this->client->encerrar(
                $id,
                $validated['servicosRealizados'] ?? [],
                isset($validated['servicoRedirecionado']) ? (int) $validated['servicoRedirecionado'] : null,
                isset($validated['novoUsuario']) ? (int) $validated['novoUsuario'] : null,
            );

            return response()->json(['atendimento' => $atendimento]);
        });
    }

    /**
     * Redirecionamento interno: transfere a senha entre setores sem novo ticket
     * (Seção 2.4) — no NovoSGA isso equivale a encerrar com servicoRedirecionado.
     */
    public function redirecionar(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'servicoRedirecionado' => ['required', 'integer', 'min:1'],
        ]);

        return $this->comFallbackNovosga(function () use ($id, $validated) {
            $atendimento = $this->client->encerrar(
                $id,
                [],
                (int) $validated['servicoRedirecionado'],
                null,
            );

            return response()->json(['atendimento' => $atendimento]);
        });
    }

    /**
     * Status do atendente (Em atendimento / Pausa / Ausente) — Seção 2.4.
     * PUT /api/filas do NovoSGA altera o status do atendimento atual.
     */
    public function status(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:EM_ATENDIMENTO,PAUSA,AUSENTE,DISPONIVEL'],
        ]);

        return $this->comFallbackNovosga(function () use ($validated) {
            $response = $this->client->alteraStatus((string) $validated['status']);

            return response()->json(['atendimento' => $response]);
        });
    }

    /**
     * Política única de degradação para chamadas ao NovoSGA (Seção 9.1):
     * qualquer falha vira um JSON de erro estruturado com o status HTTP
     * correto, nunca um 500 cru nem um silêncio que esconda indisponibilidade
     * (ex.: fila vazia por engano).
     */
    protected function comFallbackNovosga(\Closure $acao): JsonResponse
    {
        try {
            return $acao();
        } catch (NovosgaApiException $e) {
            return response()->json(['error' => $e->getMessage()], $e->status ?: 502);
        }
    }

    /**
     * Notificações do setor (pop-up "Notificação Automática" — Seção 2.3/2.6).
     */
    public function notificacoes(int $departamentoId): JsonResponse
    {
        return response()->json([
            'naoLidas' => $this->notificacoes->listarPorSetor($departamentoId, true),
        ]);
    }

    public function marcarNotificacao(int $id): JsonResponse
    {
        $this->notificacoes->marcarLida($id);

        return response()->json(['ok' => true]);
    }
}
