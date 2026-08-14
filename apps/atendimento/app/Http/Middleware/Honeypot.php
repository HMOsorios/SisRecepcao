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

        if ($request->has($campo) && $request->input($campo) !== '') {
            abort(422, 'Solicitação inválida.');
        }

        if (config('honeypot.validar_tempo') && $request->has($tempoCampo)) {
            $preenchidoEm = (int) $request->input($tempoCampo);
            if ($preenchidoEm > 0 && (now()->timestamp - $preenchidoEm) < 3) {
                abort(422, 'Solicitação muito rápida.');
            }
        }

        return $next($request);
    }
}
