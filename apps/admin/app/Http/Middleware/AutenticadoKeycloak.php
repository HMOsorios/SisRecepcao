<?php

namespace App\Http\Middleware;

use App\Services\Keycloak\KeycloakClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige autenticação via Keycloak (Seção 8.6).
 */
class AutenticadoKeycloak
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->keycloak->usuario() === null) {
            return redirect()->guest(route('auth.login'));
        }

        return $next($request);
    }
}
