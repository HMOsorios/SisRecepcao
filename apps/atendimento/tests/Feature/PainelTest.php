<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PainelTest extends TestCase
{
    public function test_index_renderiza_view_com_dados_e_mercure(): void
    {
        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
        ]);

        $response = $this->get(route('painel.index'));

        $response->assertOk();
        $response->assertSee('data-dados-url', false);
        $response->assertSee('data-mercure-url', false);
    }

    public function test_dados_normaliza_painel_do_novosga(): void
    {
        Http::fake([
            '*/api/token' => Http::response(['access_token' => 'token-teste']),
            '*/api/unidades/1/painel*' => Http::response([
                [
                    'id' => 5,
                    'senha' => ['sigla' => 'AG', 'numero' => 3],
                    'local' => 'Mesa',
                    'numeroLocal' => 2,
                    'servico' => ['nome' => 'Atendimento Geral'],
                    'prioridade' => 'Convencional',
                    'peso' => 0,
                    'cliente' => ['nome' => 'João'],
                ],
            ]),
        ]);

        $response = $this->get(route('painel.dados'));

        $response->assertOk();
        $response->assertJsonCount(1);
        $response->assertJsonPath('0.id', 5);
        $response->assertJsonPath('0.senha', 'AG 03');
        $response->assertJsonPath('0.servico', 'Atendimento Geral');
        $response->assertJsonPath('0.cliente', 'João');
    }

    public function test_dados_retorna_lista_vazia_quando_novosga_offline(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        $response = $this->get(route('painel.dados'));

        $response->assertOk();
        $response->assertJson([]);
    }
}
