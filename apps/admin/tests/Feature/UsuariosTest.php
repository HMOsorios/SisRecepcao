<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\AutenticaKeycloak;
use Tests\TestCase;

class UsuariosTest extends TestCase
{
    use AutenticaKeycloak;

    protected string $base = 'http://keycloak.test';

    protected function fakeAdminApi(): void
    {
        Http::fake([
            "{$this->base}/realms/sisrecepcao/.well-known/openid-configuration" => Http::response([
                'authorization_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/auth",
                'token_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/token",
                'userinfo_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/userinfo",
                'end_session_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/logout",
            ], 200),
            "{$this->base}/realms/sisrecepcao/protocol/openid-connect/token" => Http::response(['access_token' => 'admin-tok'], 200),
            "{$this->base}/admin/realms/sisrecepcao/users*" => Http::response([
                ['username' => 'gestor.teste', 'firstName' => 'Gestor', 'email' => 'gestor@teste.local', 'enabled' => true],
            ], 200),
        ]);
    }

    #[Test]
    public function servidor_sem_perfil_leva_403(): void
    {
        $this->autenticar();
        $this->get('/usuarios')->assertForbidden();
    }

    #[Test]
    public function admin_lista_usuarios_do_realm(): void
    {
        config()->set('keycloak.admin_user', 'realm-admin');
        config()->set('keycloak.admin_password', 'senha-secreta');
        $this->autenticar('Admin');
        $this->fakeAdminApi();

        $this->get('/usuarios')
            ->assertOk()
            ->assertSee('gestor.teste')
            ->assertSee('Perfis de acesso');
    }

    #[Test]
    public function sem_credenciais_admin_mostra_aviso(): void
    {
        config()->set('keycloak.admin_user', '');
        $this->autenticar('Admin');

        $this->get('/usuarios')
            ->assertOk()
            ->assertSee('Nenhum usuário retornado');
    }
}
