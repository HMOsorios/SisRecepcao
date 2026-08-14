<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AtendenteTest extends TestCase
{
    use RefreshDatabase;

    private function autenticar(): void
    {
        Session::put(config('keycloak.session_user_key'), [
            'sub' => 'user-1',
            'preferred_username' => 'maria.ferreira',
            'name' => 'Maria Ferreira',
            'departamento_id' => 2,
        ]);
    }

    private function fakeNovosga(): void
    {
        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
            '*/api/filas/1' => Http::response([
                ['id' => 11, 'status' => 'SENHA_EMITIDA', 'prioridade' => ['peso' => 0], 'servico' => ['nome' => 'Gabinete']],
            ]),
            '*/api/atendimentos/*' => Http::response(['id' => 11, 'senha' => ['sigla' => 'GB', 'numero' => 1]]),
            '*/api/filas' => Http::response(['status' => 'PAUSA']),
            '*/api/locais' => Http::response(['data' => [['id' => 1, 'nome' => 'Mesa 1']]]),
            '*/api/servicos' => Http::response(['data' => [['id' => 1, 'nome' => 'Atendimento Geral', 'sigla' => 'AG']]]),
            '*/api/departamentos' => Http::response(['data' => [['id' => 2, 'nome' => 'Gabinete']]]),
        ]);
    }

    public function test_redireciona_para_login_quando_nao_autenticado(): void
    {
        $response = $this->get(route('atendente.index'));

        $response->assertRedirect(route('auth.login'));
    }

    public function test_console_renderiza_para_usuario_autenticado(): void
    {
        $this->autenticar();
        $this->fakeNovosga();

        $response = $this->get(route('atendente.index'));

        $response->assertOk();
        $response->assertSee('Maria Ferreira');
        $response->assertSee('name="console"', false);
    }

    public function test_fila_retorna_json(): void
    {
        $this->autenticar();
        $this->fakeNovosga();

        $response = $this->get(route('atendente.fila'));

        $response->assertOk();
        $response->assertJsonPath('fila.0.id', 11);
        $response->assertJsonPath('proxima.id', 11);
    }

    public function test_proxima_chama_senha_da_fila(): void
    {
        $this->autenticar();
        $this->fakeNovosga();

        $response = $this->postJson(route('atendente.proxima'), [
            'local' => 1,
            'numeroLocal' => 2,
        ]);

        $response->assertOk();
        $response->assertJsonPath('atendimento.id', 11);
    }

    public function test_proxima_retorna_404_quando_fila_vazia(): void
    {
        $this->autenticar();

        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
            '*/api/filas/1' => Http::response([]),
        ]);

        $response = $this->postJson(route('atendente.proxima'), [
            'local' => 1,
            'numeroLocal' => 1,
        ]);

        $response->assertStatus(404);
    }

    public function test_status_altera_status_no_novosga(): void
    {
        $this->autenticar();
        $this->fakeNovosga();

        $response = $this->postJson(route('atendente.status'), ['status' => 'PAUSA']);

        $response->assertOk();
        Http::assertSent(fn ($request) => $request->url() === config('novosga.base_url').'/api/filas');
    }

    public function test_status_invalido_e_rejeitado(): void
    {
        $this->autenticar();

        $response = $this->postJson(route('atendente.status'), ['status' => 'QUALQUER']);

        $response->assertStatus(422);
    }

    public function test_notificacoes_do_setor(): void
    {
        $this->autenticar();

        $response = $this->get(route('atendente.notificacoes', ['departamentoId' => 2]));

        $response->assertOk();
        $response->assertJson(['naoLidas' => []]);
    }
}
