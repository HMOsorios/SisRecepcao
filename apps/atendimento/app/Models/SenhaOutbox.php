<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Registro de senha provisória emitida em modo de contingência (Seção 9.1).
 */
class SenhaOutbox extends Model
{
    protected $table = 'senha_outbox';

    protected $fillable = [
        'senha_provisoria',
        'unidade_id',
        'servico_id',
        'prioridade_id',
        'cliente',
        'metadata',
        'status',
        'tentativas',
        'data_chegada',
        'enviado_em',
        'erro',
    ];

    protected $casts = [
        'cliente' => 'array',
        'metadata' => 'array',
        'data_chegada' => 'datetime',
        'enviado_em' => 'datetime',
    ];

    public const STATUS_PENDENTE = 'pendente';

    public const STATUS_ENVIANDO = 'enviando';

    public const STATUS_ENVIADO = 'enviado';

    public const STATUS_FALHOU = 'falhou';

    /** Excedeu o limite de tentativas — precisa de intervenção manual (Seção 9.1). */
    public const STATUS_ESGOTADO = 'esgotado';
}
