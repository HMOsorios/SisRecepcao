<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected string $base = 'http://keycloak.test';

    protected function fakeKeycloak(): void
    {
        Http::fake([
            "{$this->base}/realms/sisrecepcao/.well-known/openid-configuration" => Http::response([
                'authorization_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/auth",
                'token_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/token",
                'userinfo_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/userinfo",
                'end_session_endpoint' => "{$this->base}/realms/sisrecepcao/protocol/openid-connect/logout",
            ], 200),
            "{$this->base}/realms/sisrecepcao/protocol/openid-connect/token" => Http::response([
                'access_token' => 'access-1',
                'refresh_token' => 'refresh-1',
                'token_type' => 'Bearer',
            ], 200),
            "{$this->base}/realms/sisrecepcao/protocol/openid-connect/userinfo" => Http::response([
                'sub' => 'abc-123',
                'name' => 'Servidora Teste',
                'preferred_username' => 'servidora.teste',
                'realm_access' => ['roles' => ['Servidor']],
            ], 200),
        ]);
    }

    #[Test]
    public function login_mostra_tela_de_confirmacao(): void
    {
        // A tela do Keycloak é idêntica visualmente entre os apps do mesmo
        // realm — essa confirmação evita logar com a conta errada no app
        // errado (Seção 8.6).
        $this->get(route('auth.login'))
            ->assertOk()
            ->assertSee('Portal Administrativo');
    }

    #[Test]
    public function login_iniciar_redireciona_para_keycloak(): void
    {
        $this->fakeKeycloak();

        $this->get(route('auth.login.iniciar'))
            ->assertRedirect()
            ->assertRedirectContains($this->base);
    }

    #[Test]
    public function login_ja_autenticado_vai_para_dashboard(): void
    {
        Session::put(config('keycloak.session_user_key'), [
            'sub' => 'x', 'name' => 'X', 'realm_access' => ['roles' => ['Servidor']],
        ]);

        $this->get(route('auth.login'))->assertRedirect(route('home.dashboard'));
    }

    #[Test]
    public function callback_com_state_invalido_reinicia_login(): void
    {
        Session::put(config('keycloak.session_state_key'), 'estado-esperado');
        Session::put('keycloak_code_verifier', 'verifier');

        $this->get('/auth/callback?code=abc&state=errado')
            ->assertRedirect(route('home.index'))
            ->assertSessionHas('erro');
    }

    #[Test]
    public function callback_com_erro_redireciona_com_mensagem(): void
    {
        $this->get('/auth/callback?error=access_denied')
            ->assertRedirect(route('home.index'))
            ->assertSessionHas('erro');
    }

    #[Test]
    public function callback_feliz_autentica_e_redireciona(): void
    {
        $this->fakeKeycloak();
        Session::put(config('keycloak.session_state_key'), 'estado-correto');
        Session::put('keycloak_code_verifier', 'verifier');

        $this->get('/auth/callback?code=codigo&state=estado-correto')
            ->assertRedirect(route('home.dashboard'));

        $this->assertSame('abc-123', Session::get(config('keycloak.session_user_key'))['sub']);
    }
}
