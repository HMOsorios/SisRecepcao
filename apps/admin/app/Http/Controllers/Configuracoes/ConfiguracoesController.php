<?php

namespace App\Http\Controllers\Configuracoes;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use App\Services\Configuracoes\ConfiguracaoService;
use App\Services\Deploy\DeployService;
use App\Services\Keycloak\KeycloakClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Painel de Configurações e Manutenção (exclusivo Developer — Seção 8.4).
 * Ações de deploy/migração são auditadas (Seção 8.8 — Auditoria).
 */
class ConfiguracoesController extends Controller
{
    public function __construct(
        protected readonly ConfiguracaoService $service,
        protected readonly DeployService $deploy,
        protected readonly KeycloakClient $keycloak,
    ) {}

    public function index(): View
    {
        return view('configuracoes.index', [
            'campos' => $this->service->campos(),
            'valores' => $this->service->valores(),
            'versao' => $this->service->versao(),
            'githubRepo' => config('sms.github_repo'),
            'baseDir' => base_path(),
            'auditorias' => LogAuditoria::query()->latest()->limit(20)->get(),
        ]);
    }

    public function salvar(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'campos' => ['required', 'array'],
            'campos.*' => ['nullable', 'string', 'max:500'],
        ]);

        $usuario = $this->keycloak->usuario() ?? [];
        $this->service->salvar(
            $validated['campos'],
            (string) ($usuario['sub'] ?? '?'),
            (string) ($usuario['name'] ?? $usuario['preferred_username'] ?? '?'),
            $request,
        );

        return back()->with('ok', 'Configurações institucionais atualizadas.');
    }

    public function acao(Request $request, string $acao): RedirectResponse
    {
        $usuario = $this->keycloak->usuario() ?? [];

        $disponiveis = ['git_pull', 'migrar', 'recarregar_caches', 'deploy_coolify'];
        if (! in_array($acao, $disponiveis, true)) {
            abort(404);
        }

        $resultado = match ($acao) {
            'git_pull' => $this->deploy->gitPull(base_path()),
            'migrar' => $this->deploy->migrar(base_path()),
            'recarregar_caches' => $this->deploy->recarregarCaches(base_path()),
            'deploy_coolify' => $this->deploy->deployCoolify(),
            default => ['ok' => false, 'saida' => 'Ação desconhecida.'],
        };

        LogAuditoria::registrar(
            (string) ($usuario['sub'] ?? '?'),
            (string) ($usuario['name'] ?? $usuario['preferred_username'] ?? '?'),
            'configuracoes.acao',
            ['acao' => $acao, 'ok' => $resultado['ok'], 'saida' => mb_substr($resultado['saida'], 0, 2000)],
            $request,
        );

        $request->session()->flash($resultado['ok'] ? 'ok' : 'erro', 'Ação "'.$acao.'": '.$resultado['saida']);

        return back();
    }
}
