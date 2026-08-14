<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\AutenticaKeycloak;
use Tests\TestCase;

class DiretoriaTest extends TestCase
{
    use AutenticaKeycloak;
    use RefreshDatabase;

    protected function fakeNovosga(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok'], 200),
            'http://novosga.test/api/atendimentos*' => Http::response([
                'data' => [
                    [
                        'id' => 1, 'status' => 'FINALIZADO',
                        'servico' => ['nome' => 'Atendimento Geral'],
                        'dataChegada' => now()->subMinutes(30)->toIso8601String(),
                        'tempoEspera' => 300,
                    ],
                ],
            ], 200),
            'http://novosga.test/api/filas/1' => Http::response(['data' => []], 200),
        ]);
    }

    #[Test]
    public function servidor_sem_perfil_de_diretoria_leva_403(): void
    {
        $this->autenticar();
        $this->get('/diretoria')->assertForbidden();
    }

    #[Test]
    public function diretoria_acessa_o_painel(): void
    {
        Cache::forget('novosga_token');
        $this->autenticar('Diretoria');
        $this->fakeNovosga();

        $this->get('/diretoria')
            ->assertOk()
            ->assertSee('Painel da Diretoria Administrativa')
            ->assertSee('Atendimentos por setor');
    }

    #[Test]
    public function admin_acessa_o_painel_da_diretoria(): void
    {
        Cache::forget('novosga_token');
        $this->autenticar('Admin');
        $this->fakeNovosga();

        $this->get('/diretoria')->assertOk();
    }

    #[Test]
    public function relatorio_pdf_download(): void
    {
        Cache::forget('novosga_token');
        $this->autenticar('Diretoria');
        $this->fakeNovosga();

        $this->get('/diretoria/relatorio')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }
}
