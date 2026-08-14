<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Proteção anti-bot/honeypot (Seção 8.8) para formulários públicos (totem).
 *
 * Um campo "armadilha" é renderizado escondido via CSS (fora da tela) e
 * preenchido apenas por bots; formulários legítimos nunca o preenchem.
 */
class Honeypot
{
    public function handle(Request $request, Closure $next): Response
    {
        $campo = config('honeypot.field', 'website');
        $tempoCampo = config('honeypot.tempo_field', 'empresa');

        // filled() (não has() + !== '') — o middleware global
        // ConvertEmptyStringsToNull roda antes deste e converte '' em null;
        // "!== ''" nunca detecta isso e barra até submissão legítima.
        if (filled($request->input($campo))) {
            abort(422, 'Solicitação inválida.');
        }

        if (config('honeypot.validar_tempo') && filled($request->input($tempoCampo))) {
            $preenchidoEm = (int) $request->input($tempoCampo);
            $minimo = (int) config('honeypot.tempo_minimo', 3);
            if ($preenchidoEm > 0 && (now()->timestamp - $preenchidoEm) < $minimo) {
                abort(422, 'Solicitação muito rápida.');
            }
        }

        return $next($request);
    }
}
