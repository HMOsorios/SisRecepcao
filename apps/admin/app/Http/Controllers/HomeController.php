<?php

namespace App\Http\Controllers;

use App\Services\Keycloak\KeycloakClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Página institucional pública (Seção 8.5) e redirecionamento por perfil.
 */
class HomeController extends Controller
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function index(): View
    {
        return view('home.index');
    }

    public function dashboard(): RedirectResponse|View
    {
        $usuario = $this->keycloak->usuario();

        if ($usuario === null) {
            return redirect()->route('auth.login');
        }

        if ($this->keycloak->temPerfil('Developer', 'Admin', 'Diretoria')) {
            return redirect()->route('diretoria.index');
        }

        return redirect()->route('bi.index');
    }
}
