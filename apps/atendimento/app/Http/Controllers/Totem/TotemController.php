<?php

namespace App\Http\Controllers\Totem;

use App\Http\Controllers\Controller;
use App\Models\SenhaOutbox;
use App\Services\Fila\CatalogoService;
use App\Services\Fila\EmissaoSenhaService;
use App\Services\Novosga\NovosgaClient;
use App\Services\Totem\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Módulo 2.1 — Totem de autoatendimento (kiosk PWA, touchscreen).
 */
class TotemController extends Controller
{
    public function __construct(
        protected readonly CatalogoService $catalogo,
        protected readonly EmissaoSenhaService $emissao,
    ) {}

    public function index(): View
    {
        return view('totem.index', [
            'servicos' => $this->catalogo->servicos(),
            'prioridades' => $this->catalogo->prioridades(),
            'audio' => config('totem.audio_habilitado'),
        ]);
    }

    public function dados(): JsonResponse
    {
        // Os nomes dos campos honeypot NÃO entram aqui: o formulário já os
        // recebe embutidos no HTML server-side (totem/index.blade.php) e o
        // totem.js os lê do DOM — expor os nomes por este endpoint público
        // anularia o sigilo do honeypot (Seção 8.8).
        return response()->json([
            'servicos' => $this->catalogo->servicos(),
            'prioridades' => $this->catalogo->prioridades(),
            'unidadeId' => $this->catalogo->unidadeId(),
        ]);
    }

    /**
     * Emissão de senha (categoria + prioridade). Protegido com honeypot e
     * rate limit por IP (Seção 8.8).
     */
    public function emitir(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'servico' => ['required', 'integer', 'min:1'],
            'prioridade' => ['required', 'integer', 'min:1'],
            'nome' => ['nullable', 'string', 'max:120'],
            'documento' => ['nullable', 'string', 'max:30'],
            'metadata.totem' => ['nullable'],
        ]);

        $cliente = null;
        if (! empty($validated['nome']) || ! empty($validated['documento'])) {
            $cliente = [
                'nome' => $validated['nome'] ?? '',
                'documento' => preg_replace('/\D/', '', (string) ($validated['documento'] ?? '')),
            ];
        }

        $resultado = $this->emissao->emitir(
            (int) $validated['servico'],
            (int) $validated['prioridade'],
            $cliente,
            ['totem' => true],
        );

        $provisional = (bool) $resultado['provisional'];
        $ticket = $provisional ? $resultado['outbox']->toArray() : $resultado['atendimento'];
        $ticketId = $ticket['id'] ?? $ticket['senha_provisoria'];

        session()->flash('ticket', $ticket);
        session()->flash('provisional', $provisional);

        if ($request->expectsJson()) {
            return response()->json([
                'provisional' => $provisional,
                'ticket' => $ticket,
            ], 201);
        }

        return redirect()->route('totem.ticket', ['id' => $ticketId]);
    }

    /**
     * Confirmação do ticket (senha, categoria, QR Code para acompanhamento).
     */
    public function ticket(string $id, NovosgaClient $client, QrCodeService $qr): View
    {
        $ticket = session('ticket');
        $provisional = (bool) session('provisional');

        if ($ticket === null) {
            if (is_numeric($id) && ! $provisional) {
                try {
                    $ticket = $client->atendimento((int) $id);
                } catch (\Throwable) {
                    $ticket = ['id' => (int) $id];
                }
            } else {
                $ticket = SenhaOutbox::query()
                    ->where('senha_provisoria', $id)
                    ->first()?->toArray() ?? ['id' => $id];
            }
        }

        $senha = $this->senhaDeTicket($ticket);

        return view('totem.ticket', [
            'ticket' => $ticket,
            'senha' => $senha,
            'provisional' => $provisional,
            'qrDataUri' => $qr->dataUri($qr->urlAcompanhamento((string) $senha)),
        ]);
    }

    /**
     * Extrai o número da senha de um ticket do NovoSGA ou de um outbox.
     *
     * @param  array<string,mixed>  $ticket
     */
    protected function senhaDeTicket(array $ticket): string
    {
        if (! empty($ticket['senha_provisoria'])) {
            return (string) $ticket['senha_provisoria'];
        }

        $senha = $ticket['senha'] ?? null;
        if (is_array($senha)) {
            return trim(((string) ($senha['sigla'] ?? '')).' '.(string) ($senha['numero'] ?? ''));
        }

        return (string) ($ticket['senha'] ?? '#'.($ticket['id'] ?? ''));
    }
}
