<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Keycloak\KeycloakClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Autenticação via Keycloak (IdP central — Seção 5.4/8.6).
 */
class AuthController extends Controller
{
    public function __construct(protected readonly KeycloakClient $keycloak) {}

    /**
     * Inicia o fluxo de login redirecionando para o Keycloak.
     */
    public function login(): RedirectResponse
    {
        return redirect()->away($this->keycloak->authorizationUrl());
    }

    /**
     * Callback após autenticação no Keycloak (troca do code por token).
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->filled('error')) {
            return redirect()->route('auth.login')->withErrors(['auth' => (string) $request->input('error_description', 'Falha na autenticação.')]);
        }

        $code = (string) $request->input('code', '');
        $state = $request->input('state');
        $esperado = session(config('keycloak.session_state_key'));

        if ($code === '' || $state !== $esperado) {
            abort(419, 'State inválido ou código ausente — possível CSRF.');
        }

        $this->keycloak->handleCallback($code);

        // Previne session fixation: uma sessão autenticada nunca deve manter
        // o ID de sessão de antes do login (Seção 8.8).
        $request->session()->regenerate();

        return redirect()->intended(route('atendente.index'));
    }

    /**
     * Redireciona ao logout do Keycloak e limpa a sessão local.
     */
    public function logout(Request $request): RedirectResponse
    {
        $url = $this->keycloak->logoutUrl(route('totem.index'));

        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->away($url);
    }

    /**
     * Encerra apenas a sessão local (sem chamar o Keycloak).
     */
    public function sair(Request $request): RedirectResponse
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect()->route('totem.index');
    }
}
