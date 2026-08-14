<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cracha extends Model
{
    protected $fillable = [
        'visitante_id',
        'codigo',
        'status',
        'emitido_em',
        'expira_em',
        'devolvido_em',
    ];

    protected $casts = [
        'emitido_em' => 'datetime',
        'expira_em' => 'datetime',
        'devolvido_em' => 'datetime',
    ];

    public const STATUS_ATIVO = 'ativo';

    public const STATUS_INATIVO = 'inativo';

    /** @return BelongsTo<Visitante, $this> */
    public function visitante(): BelongsTo
    {
        return $this->belongsTo(Visitante::class);
    }

    public function isValido(): bool
    {
        return $this->status === self::STATUS_ATIVO
            && ($this->expira_em === null || $this->expira_em->isFuture());
    }
}
