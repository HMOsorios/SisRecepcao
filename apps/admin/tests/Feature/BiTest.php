<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\AutenticaKeycloak;
use Tests\TestCase;

class BiTest extends TestCase
{
    use AutenticaKeycloak;

    protected function fakeNovosga(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok'], 200),
            'http://novosga.test/api/atendimentos*' => Http::response([
                'data' => [
                    [
                        'id' => 1, 'status' => 'FINALIZADO',
                        'servico' => ['nome' => 'Atendimento Geral'],
                        'senha' => ['sigla' => 'AG', 'numero' => '05'],
                        'dataChegada' => now()->subMinutes(30)->toIso8601String(),
                        'tempoEspera' => 300,
                    ],
                ],
            ], 200),
            'http://novosga.test/api/filas/1' => Http::response(['data' => []], 200),
        ]);
    }

    #[Test]
    public function servidor_acessa_bi(): void
    {
        $this->autenticar();
        $this->get('/bi/resumo')->assertOk()->assertSee('Indicadores de Atendimento');
    }

    #[Test]
    public function api_resumo_retorna_indicadores(): void
    {
        Cache::forget('novosga_token');
        $this->autenticar();
        $this->fakeNovosga();

        $this->getJson('/bi/api/resumo')
            ->assertOk()
            ->assertJsonPath('total_dia', 1)
            ->assertJsonPath('em_fila', 0)
            ->assertJsonStructure(['atualizado_em']);
    }

    #[Test]
    public function api_busca_filtra_por_termo(): void
    {
        Cache::forget('novosga_token');
        $this->autenticar();
        $this->fakeNovosga();

        $this->getJson('/bi/api/buscar?q=geral')
            ->assertOk()
            ->assertJsonCount(1, 'resultados')
            ->assertJsonPath('resultados.0.id', 1);
    }

    #[Test]
    public function sem_autenticacao_nao_acessa_api(): void
    {
        $this->getJson('/bi/api/resumo')->assertRedirect();
    }

    #[Test]
    public function usuario_autenticado_sem_nenhum_perfil_leva_403(): void
    {
        // Regressão: as rotas de BI ficaram autenticadas mas sem middleware
        // `perfil:` — qualquer conta do Keycloak, mesmo sem role nenhuma,
        // conseguia acessar. AutenticaKeycloak::autenticar() sempre inclui
        // "Servidor", então este teste monta a sessão diretamente.
        Session::put(config('keycloak.session_user_key'), [
            'sub' => 'sem-perfil',
            'preferred_username' => 'terceiro.sem.role',
            'name' => 'Terceiro',
            'realm_access' => ['roles' => []],
            'resource_access' => ['sisrecepcao-admin' => ['roles' => []]],
        ]);

        $this->get('/bi/resumo')->assertForbidden();
    }
}
