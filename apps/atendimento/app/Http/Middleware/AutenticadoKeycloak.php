<?php

namespace App\Http\Middleware;

use App\Services\Keycloak\KeycloakClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige autenticação via Keycloak (console do atendente — Módulo 2.4).
 */
class AutenticadoKeycloak
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->keycloak->usuario() === null) {
            return redirect()->guest(route('auth.login'));
        }

        $response = $next($request);

        // Sem isso, o navegador pode reexibir a página autenticada do cache
        // local após o logout, dando a falsa impressão de sessão ainda ativa.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
