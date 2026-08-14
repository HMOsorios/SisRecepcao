<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Keycloak\KeycloakClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    /**
     * Tela de confirmação antes do Keycloak — a tela de login do Keycloak é
     * idêntica visualmente para todos os apps/clients do mesmo realm, então
     * sem isso é fácil confundir e logar com a conta errada no app errado.
     */
    public function login(): RedirectResponse|View
    {
        if ($this->keycloak->usuario() !== null) {
            return redirect()->route('home.dashboard');
        }

        return view('auth.login');
    }

    public function iniciar(): RedirectResponse
    {
        return redirect()->away($this->keycloak->authorizationUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('home.index')->with('erro', 'Autenticação cancelada ou falhou.');
        }

        $code = (string) $request->query('code');

        if ($code === '' || $request->query('state') !== Session::get(config('keycloak.session_state_key'))) {
            // Não é um erro fatal para o usuário: acontece quando a URL de
            // callback é revisitada (voltar do navegador, aba antiga) depois
            // de um novo login ter sido iniciado. Mesmo padrão do caso
            // "error" acima — volta à home com aviso, em vez de travar numa
            // página de erro sem saída.
            return redirect()->route('home.index')->with('erro', 'Sessão de login expirada. Clique em "Acessar o sistema" para tentar de novo.');
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
