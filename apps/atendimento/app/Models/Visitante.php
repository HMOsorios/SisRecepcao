<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Visitante extends Model
{
    protected $fillable = [
        'nome',
        'documento',
        'email',
        'telefone',
        'genero',
        'data_nascimento',
        'tipo_documento',
        'setor_destino',
        'sala_destino',
        'foto',
        'observacao',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'purged_at' => 'datetime',
    ];

    /** @return HasMany<Cracha, $this> */
    public function crachas(): HasMany
    {
        return $this->hasMany(Cracha::class);
    }

    /**
     * Mascara o documento (CPF) para exibição pública — LGPD (Seção 2.5).
     */
    public function getDocumentoMascaradoAttribute(): string
    {
        return Mascaramento::cpf((string) $this->documento);
    }
}
