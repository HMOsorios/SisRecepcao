<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * Trilha de auditoria das ações administrativas (Seção 8.8 — Auditoria).
 */
class LogAuditoria extends Model
{
    protected $fillable = [
        'usuario_id',
        'usuario_nome',
        'acao',
        'detalhes',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'detalhes' => 'array',
    ];

    /**
     * Registra uma ação administrativa.
     *
     * @param  array<string,mixed>|null  $detalhes
     */
    public static function registrar(
        string $usuarioId,
        string $usuarioNome,
        string $acao,
        ?array $detalhes = null,
        ?Request $request = null,
    ): self {
        return self::create([
            'usuario_id' => $usuarioId,
            'usuario_nome' => $usuarioNome,
            'acao' => $acao,
            'detalhes' => $detalhes,
            'ip' => $request?->ip(),
            'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 250) : null,
        ]);
    }
}
