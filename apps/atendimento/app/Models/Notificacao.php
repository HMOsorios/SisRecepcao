<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Notificação de chegada de visitante disparada para o setor de destino.
 */
class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = [
        'atendimento_id',
        'departamento_id',
        'tipo',
        'payload',
        'lida_em',
    ];

    protected $casts = [
        'payload' => 'array',
        'lida_em' => 'datetime',
    ];

    public function marcarComoLida(): void
    {
        $this->lida_em = now();
        $this->save();
    }
}
