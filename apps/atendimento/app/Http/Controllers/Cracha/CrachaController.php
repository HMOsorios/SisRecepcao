<?php

namespace App\Http\Controllers\Cracha;

use App\Http\Controllers\Controller;
use App\Models\Cracha;
use App\Models\Mascaramento;
use App\Services\Cracha\CrachaService;
use App\Services\Cracha\OcrContrato;
use App\Services\Fila\CatalogoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Módulo 2.3 — Cadastro rápido de visitante + crachá temporário.
 */
class CrachaController extends Controller
{
    public function __construct(
        protected readonly CrachaService $service,
        protected readonly OcrContrato $ocr,
    ) {}

    public function form(): View
    {
        return view('cracha.form', [
            'setores' => app(CatalogoService::class)->departamentos(),
        ]);
    }

    /**
     * Cadastra o visitante (com OCR opcional) e emite o crachá temporário.
     */
    public function registrar(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'documento' => ['required', 'string', 'max:30'],
            'tipo_documento' => ['required', 'in:CPF,RG'],
            'email' => ['nullable', 'email', 'max:120'],
            'telefone' => ['nullable', 'string', 'max:25'],
            'genero' => ['nullable', 'in:M,F,O'],
            'data_nascimento' => ['nullable', 'date'],
            'setor_destino' => ['nullable', 'string', 'max:120'],
            'sala_destino' => ['nullable', 'string', 'max:120'],
            'observacao' => ['nullable', 'string', 'max:500'],
            'foto' => ['nullable', 'image', 'max:2048'],
            'imagem_documento' => ['nullable', 'image', 'max:4096'],
        ]);

        // OCR: tenta ler o documento a partir de imagem enviada (opcional).
        if ($request->hasFile('imagem_documento') && $request->file('imagem_documento')->isValid()) {
            $dadosOcr = $this->ocr->lerDocumento((string) $request->file('imagem_documento')->get());

            if (is_array($dadosOcr)) {
                // Os dados do OCR passam pelas mesmas regras já aplicadas ao
                // formulário manual — nunca gravar valor fora do padrão esperado.
                $ocrValidado = Validator::make([
                    'nome' => $dadosOcr['nome'] ?? $validated['nome'],
                    'documento' => $dadosOcr['numero'] ?? $validated['documento'],
                ], [
                    'nome' => ['required', 'string', 'max:120'],
                    'documento' => ['required', 'string', 'max:30'],
                ])->valid();

                $validated['nome'] = $ocrValidado['nome'] ?? $validated['nome'];
                $validated['documento'] = $ocrValidado['documento'] ?? $validated['documento'];
            }
        }

        $visitante = $this->service->registrar($validated);

        if ($request->hasFile('foto')) {
            $visitante->update([
                'foto' => $request->file('foto')->store('crachas/fotos', 'public'),
            ]);
        }

        $cracha = $this->service->emitirCracha(
            $visitante,
            (int) config('sms.cracha_validade_minutos', 120),
        );

        if ($request->expectsJson()) {
            $visitante = $cracha->visitante;

            return response()->json([
                'cracha' => [
                    'id' => $cracha->id,
                    'codigo' => $cracha->codigo,
                    'status' => $cracha->status,
                    'expira_em' => $cracha->expira_em,
                ],
                // LGPD (Seção 2.5): mesma máscara aplicada na via impressa —
                // nunca devolver documento/telefone/e-mail em texto puro por API.
                'visitante' => [
                    'nome' => $visitante->nome,
                    'documento_mascarado' => Mascaramento::cpf((string) $visitante->documento),
                    'setor_destino' => $visitante->setor_destino,
                    'sala_destino' => $visitante->sala_destino,
                ],
                'crachalUrl' => route('cracha.imprimir', $cracha),
            ], 201);
        }

        return redirect()->route('cracha.imprimir', $cracha);
    }

    /**
     * Impressão do crachá temporário (nome, foto, CPF mascarado, sala).
     */
    public function imprimir(Cracha $cracha): View
    {
        return view('cracha.badge', [
            'cracha' => $cracha->load('visitante'),
            'cpfMascarado' => $this->service->cpfMascarado((string) $cracha->visitante->documento),
        ]);
    }

    public function devolver(Cracha $cracha): RedirectResponse
    {
        $this->service->devolverCracha($cracha);

        return back()->with('ok', 'Crachá devolvido e invalidado.');
    }
}
