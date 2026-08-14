<?php

namespace App\Http\Middleware;

use App\Services\Keycloak\KeycloakClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Autorização por perfil (Developer/Admin/Servidor — Seção 8.3).
 * Uso: 'perfil:Developer,Admin' no grupo de rotas.
 */
class AutorizadoPerfil
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function handle(Request $request, Closure $next, string ...$perfis): Response
    {
        if (! $this->keycloak->temPerfil(...$perfis)) {
            abort(403, 'Acesso restrito ao perfil '.implode('/', $perfis).'.');
        }

        return $next($request);
    }
}
