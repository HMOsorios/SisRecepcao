<?php

namespace App\Http\Controllers\Usuarios;

use App\Http\Controllers\Controller;
use App\Services\Keycloak\KeycloakClient;
use Illuminate\View\View;

/**
 * Gestão de usuários (Seção 8.3) — listagem do realm via Admin REST API.
 */
class UsuariosController extends Controller
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function index(): View
    {
        return view('usuarios.index', [
            'usuarios' => $this->keycloak->usuarios(100) ?? [],
            'perfis' => $this->keycloak->perfis(),
        ]);
    }
}
