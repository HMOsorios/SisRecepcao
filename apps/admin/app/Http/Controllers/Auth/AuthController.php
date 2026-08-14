<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Keycloak\KeycloakClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    public function login(): RedirectResponse
    {
        if ($this->keycloak->usuario() !== null) {
            return redirect()->route('home.dashboard');
        }

        return redirect()->away($this->keycloak->authorizationUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('home.index')->with('erro', 'Autenticação cancelada ou falhou.');
        }

        $code = (string) $request->query('code');

        if ($code === '' || $request->query('state') !== Session::get(config('keycloak.session_state_key'))) {
            abort(401, 'Parâmetro de autenticação inválido.');
        }

        $this->keycloak->handleCallback($code);

        // Previne session fixation: uma sessão autenticada nunca deve manter
        // o ID de sessão de antes do login (Seção 8.8).
        Session::regenerate();

        return redirect()->route('home.dashboard');
    }

    public function logout(): RedirectResponse
    {
        $url = $this->keycloak->logoutUrl(route('home.index'));

        // Flush completo (não só as chaves de usuário) — access_token e
        // refresh_token gravados por handleCallback() não podem sobreviver
        // ao logout na sessão local (Seção 8.8).
        Session::flush();
        Session::regenerate();

        return redirect()->away($url);
    }

    public function sair(): RedirectResponse
    {
        Session::flush();

        return redirect()->route('home.index');
    }
}
