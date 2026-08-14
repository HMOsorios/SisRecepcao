<?php

namespace App\Models;

/**
 * Mascaramento de dados pessoais em telas públicas/relatórios — LGPD (Seção 2.5).
 */
class Mascaramento
{
    public static function cpf(string $documento): string
    {
        $digits = preg_replace('/\D/', '', $documento) ?? '';

        if (strlen($digits) !== 11) {
            return static::generic($documento);
        }

        return substr($digits, 0, 3).'.***.***-'.substr($digits, -2);
    }

    public static function rg(string $documento): string
    {
        return static::generic($documento);
    }

    /**
     * Mascara os caracteres centrais de um texto curto (nome/email/tel).
     */
    public static function generic(string $value): string
    {
        if (mb_strlen($value) <= 2) {
            return str_repeat('*', mb_strlen($value));
        }

        $first = mb_substr($value, 0, 1);
        $last = mb_substr($value, -1);

        return $first.str_repeat('*', max(3, mb_strlen($value) - 2)).$last;
    }

    /**
     * Mascara um número de telefone para exibição em telas públicas/relatórios
     * — LGPD (Seção 2.5). Mantém DDD e os 2 últimos dígitos visíveis.
     */
    public static function telefone(string $telefone): string
    {
        $digits = preg_replace('/\D/', '', $telefone) ?? '';

        if (strlen($digits) === 11) {
            return '('.substr($digits, 0, 2).') '.substr($digits, 2, 1).'****-**'.substr($digits, -2);
        }

        if (strlen($digits) === 10) {
            return '('.substr($digits, 0, 2).') ****-**'.substr($digits, -2);
        }

        return static::generic($telefone);
    }
}
