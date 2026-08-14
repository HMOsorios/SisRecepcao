<?php

namespace App\Http\Controllers\Diretoria;

use App\Http\Controllers\Controller;
use App\Services\Bi\BiService;
use App\Services\Configuracoes\ConfiguracaoService;
use App\Services\Keycloak\KeycloakClient;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Painel da Diretoria Administrativa (Seção 8.1) — indicadores consolidados
 * e relatório imprimível em PDF para prestação de contas.
 */
class DiretoriaController extends Controller
{
    public function __construct(
        protected readonly BiService $bi,
        protected readonly KeycloakClient $keycloak,
        protected readonly ConfiguracaoService $configuracoes,
    ) {}

    public function index(): View
    {
        return view('painel.diretoria', [
            'resumo' => $this->bi->resumo(),
            'porSetor' => $this->bi->porSetor(),
            'porStatus' => $this->bi->porStatus(),
            'usuarioNome' => $this->keycloak->usuario()['name'] ?? 'Diretoria',
            'atualizadoEm' => now(),
        ]);
    }

    /**
     * Relatório PDF gerado a partir dos mesmos dados do painel (Seção 8.1).
     */
    public function relatorio(): Response
    {
        $pdf = Pdf::loadView('painel.pdf', [
            'orgao' => $this->configuracoes->versao(),
            'resumo' => $this->bi->resumo(),
            'porSetor' => $this->bi->porSetor(),
            'porStatus' => $this->bi->porStatus(),
            'geradoEm' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('relatorio-diretoria-'.now()->format('Ymd-His').'.pdf');
    }
}
