<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\Session;

/**
 * Autentica um usuário de teste colocando o userinfo (Keycloak) na sessão —
 * espelha o que AuthController::callback faz.
 */
trait AutenticaKeycloak
{
    protected function autenticar(string ...$perfis): array
    {
        $roles = array_values(array_unique(array_merge(['Servidor'], $perfis)));

        $usuario = [
            'sub' => 'usuario-teste',
            'preferred_username' => 'gestor.teste',
            'name' => 'Gestor de Teste',
            'email' => 'gestor@teste.local',
            'realm_access' => ['roles' => $roles],
            'resource_access' => [
                'sisrecepcao-admin' => ['roles' => $roles],
            ],
        ];

        Session::put(config('keycloak.session_user_key'), $usuario);
        Session::put(config('keycloak.session_token_key'), 'access-token-teste');

        return $usuario;
    }

    protected function desautenticar(): void
    {
        Session::flush();
    }
}
