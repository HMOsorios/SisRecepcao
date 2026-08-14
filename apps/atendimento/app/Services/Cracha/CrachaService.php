<?php

namespace App\Services\Cracha;

use App\Models\Cracha;
use App\Models\Mascaramento;
use App\Models\Visitante;
use Illuminate\Support\Str;

/**
 * Regras do Módulo 2.3 — cadastro rápido de visitante e crachá temporário.
 */
class CrachaService
{
    /**
     * Cadastra um visitante na recepção (com possibilidade de foto/documento).
     *
     * @param  array<string,mixed>  $data
     */
    public function registrar(array $data): Visitante
    {
        $data['documento'] = $this->somenteDigitos((string) ($data['documento'] ?? ''));

        return Visitante::create($data);
    }

    /**
     * Emite um crachá temporário com código único e validade.
     */
    public function emitirCracha(Visitante $visitante, int $duracaoMinutos = 120): Cracha
    {
        $codigo = $this->gerarCodigo();

        return Cracha::create([
            'visitante_id' => $visitante->id,
            'codigo' => $codigo,
            'status' => Cracha::STATUS_ATIVO,
            'emitido_em' => now(),
            'expira_em' => now()->addMinutes($duracaoMinutos),
        ]);
    }

    public function devolverCracha(Cracha $cracha): Cracha
    {
        $cracha->update([
            'status' => Cracha::STATUS_INATIVO,
            'devolvido_em' => now(),
        ]);

        return $cracha->fresh();
    }

    /**
     * Mascara o CPF para exibição no crachá (LGPD — Seção 2.5).
     */
    public function cpfMascarado(string $documento): string
    {
        return Mascaramento::cpf($documento);
    }

    protected function gerarCodigo(): string
    {
        $codigo = null;

        do {
            $codigo = strtoupper(Str::random(6));
        } while (Cracha::query()->where('codigo', $codigo)->exists());

        return $codigo;
    }

    protected function somenteDigitos(string $valor): string
    {
        return preg_replace('/\D/', '', $valor) ?? '';
    }
}
