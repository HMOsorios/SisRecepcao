<?php

namespace Tests\Feature;

use App\Models\SenhaOutbox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TotemEmitirTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_renderiza_servicos_e_prioridades(): void
    {
        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
            '*/api/servicos' => Http::response(['data' => [
                ['id' => 1, 'nome' => 'Atendimento Geral', 'sigla' => 'AG'],
            ]]),
            '*/api/prioridades' => Http::response(['data' => [
                ['id' => 1, 'nome' => 'Convencional', 'peso' => 0],
                ['id' => 2, 'nome' => 'Idoso (60+)', 'peso' => 100],
            ]]),
        ]);

        $response = $this->get(route('totem.index'));

        $response->assertOk();
        $response->assertSee('Atendimento Geral');
        $response->assertSee('Idoso (60+)');
    }

    public function test_emitir_em_modo_simulacao_gera_senha_provisoria(): void
    {
        config(['novosga.simulation' => true]);

        $response = $this->post(route('totem.emitir'), [
            'servico' => 1,
            'prioridade' => 2,
            'nome' => 'Maria da Silva',
            'documento' => '123.456.789-01',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('senha_outbox', [
            'servico_id' => 1,
            'prioridade_id' => 2,
            'status' => SenhaOutbox::STATUS_PENDENTE,
        ]);

        $outbox = SenhaOutbox::query()->first();
        $this->assertStringStartsWith('OFF-', $outbox->senha_provisoria);
        $this->assertSame(['totem' => true], $outbox->metadata);
    }

    public function test_emitir_integra_com_novosga(): void
    {
        config(['novosga.simulation' => false]);

        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
            '*/api/distribui' => Http::response([
                'id' => 42,
                'senha' => ['sigla' => 'AG', 'numero' => 7],
            ]),
        ]);

        $response = $this->post(route('totem.emitir'), [
            'servico' => 1,
            'prioridade' => 1,
        ]);

        $response->assertRedirect(route('totem.ticket', ['id' => 42]));
        $this->assertDatabaseCount('senha_outbox', 0);

        Http::assertSent(fn ($request) => $request->url() === config('novosga.base_url').'/api/distribui');
    }

    public function test_emitir_rejeita_honeypot_preenchido(): void
    {
        config(['novosga.simulation' => true]);

        $response = $this->post(route('totem.emitir'), [
            'servico' => 1,
            'prioridade' => 1,
            config('honeypot.field') => 'http://bot.example',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('senha_outbox', 0);
    }

    public function test_emitir_rejeita_submissao_muito_rapida(): void
    {
        config(['novosga.simulation' => true]);

        $response = $this->post(route('totem.emitir'), [
            'servico' => 1,
            'prioridade' => 1,
            config('honeypot.tempo_field') => (string) now()->timestamp,
        ]);

        $response->assertStatus(422);
    }

    public function test_ticket_de_senha_provisoria_renderiza_qr(): void
    {
        config(['novosga.simulation' => true]);

        $this->post(route('totem.emitir'), ['servico' => 1, 'prioridade' => 1]);
        $outbox = SenhaOutbox::query()->first();

        $response = $this->get(route('totem.ticket', ['id' => $outbox->senha_provisoria]));

        $response->assertOk();
        $response->assertSee($outbox->senha_provisoria);
    }

    public function test_dados_cai_no_catalogo_de_fallback(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        $response = $this->get(route('totem.dados'));

        $response->assertOk();
        $response->assertJsonPath('unidadeId', 1);
        $this->assertNotEmpty($response->json('servicos'));
        $this->assertSame('Atendimento Geral', $response->json('servicos.0.nome'));
    }
}
