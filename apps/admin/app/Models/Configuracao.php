<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Configurações institucionais (chave → valor) do painel de manutenção.
 */
class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'chave',
        'valor',
        'tipo',
        'descricao',
    ];

    /**
     * Lê uma configuração (via cache agregado — allCached()).
     */
    public static function get(string $chave, mixed $padrao = null): mixed
    {
        return static::allCached()[$chave] ?? $padrao;
    }

    /**
     * Todas as configurações em um único mapa chave => valor, com uma única
     * leitura ao banco (Cache::remember) em vez de uma consulta por chave —
     * evita N idas ao cache/banco quando várias chaves são lidas em sequência
     * (ex.: ConfiguracaoService::valores()).
     *
     * @return array<string,string>
     */
    public static function allCached(): array
    {
        return Cache::remember('configuracoes_all', 600, fn () => self::query()->pluck('valor', 'chave')->all());
    }

    /**
     * Grava (ou atualiza) uma configuração e invalida o cache agregado.
     */
    public static function set(string $chave, mixed $valor, string $tipo = 'string', ?string $descricao = null): void
    {
        self::query()->updateOrCreate(
            ['chave' => $chave],
            ['valor' => is_scalar($valor) ? (string) $valor : json_encode($valor), 'tipo' => $tipo, 'descricao' => $descricao],
        );

        Cache::forget('configuracoes_all');
    }
}
