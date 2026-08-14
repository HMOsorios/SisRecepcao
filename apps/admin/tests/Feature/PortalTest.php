<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PortalTest extends TestCase
{
    #[Test]
    public function pagina_inicial_publica_carrega(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Bem-vindo ao SisRecepção')
            ->assertSee('SisPec')
            ->assertSee('Entrar');
    }

    #[Test]
    public function paginas_legais_carregam(): void
    {
        $this->get(route('legal.termos'))->assertOk()->assertSee('Termos de Uso');
        $this->get(route('legal.privacidade'))->assertOk()->assertSee('Política de Privacidade');
        $this->get(route('legal.lgpd'))->assertOk()->assertSee('LGPD');
    }

    #[Test]
    public function health_check_responde_versao_e_novosga(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response([], 503),
        ]);

        $this->getJson('/health')
            ->assertOk()
            ->assertJson([
                'status' => 'ok',
                'app' => 'sisrecepcao-admin',
                'versao' => config('app.version'),
            ])
            ->assertJsonPath('novosga', false);
    }

    #[Test]
    public function area_autenticada_redireciona_para_login(): void
    {
        $this->get('/bi/resumo')->assertRedirect(route('auth.login'));
        $this->get('/diretoria')->assertRedirect(route('auth.login'));
        $this->get('/configuracoes')->assertRedirect(route('auth.login'));
    }
}
