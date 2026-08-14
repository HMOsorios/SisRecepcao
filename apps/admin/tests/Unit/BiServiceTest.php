<?php

namespace Tests\Unit;

use App\Services\Bi\BiService;
use App\Services\Novosga\NovosgaClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BiServiceTest extends TestCase
{
    protected function service(bool $simulation = false, int $amostra = 100): BiService
    {
        return new BiService(
            client: new NovosgaClient('http://novosga.test', 'x', '', '', '', 3, 1),
            simulation: $simulation,
            unidadeId: 1,
            amostra: $amostra,
        );
    }

    protected function fakeNovosga(): void
    {
        $hoje = now();
        $ontem = now()->subDay();

        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok'], 200),
            'http://novosga.test/api/atendimentos*' => Http::response([
                'data' => [
                    [
                        'id' => 1, 'status' => 'FINALIZADO',
                        'servico' => ['nome' => 'Atendimento Geral'],
                        'dataChegada' => $hoje->copy()->subMinutes(40)->toIso8601String(),
                        'tempoEspera' => 600, 'tempoAtendimento' => 300,
                    ],
                    [
                        'id' => 2, 'status' => 'FINALIZADO',
                        'servico' => ['nome' => 'Gabinete'],
                        'dataChegada' => $hoje->copy()->subMinutes(20)->toIso8601String(),
                        'tempoEspera' => 300, 'tempoAtendimento' => 900,
                    ],
                    [
                        'id' => 3, 'status' => 'EM_ATENDIMENTO',
                        'servico' => ['nome' => 'Atendimento Geral'],
                        'dataChegada' => $hoje->copy()->subMinutes(5)->toIso8601String(),
                    ],
                    [
                        'id' => 4, 'status' => 'NAO_COMPARECEU',
                        'servico' => ['nome' => 'Ouvidoria'],
                        'dataChegada' => $ontem->copy()->subMinutes(60)->toIso8601String(),
                        'tempoEspera' => 900,
                    ],
                ],
            ], 200),
            'http://novosga.test/api/filas/1' => Http::response([
                'data' => [
                    ['id' => 90, 'status' => 'SENHA_EMITIDA'],
                    ['id' => 91, 'status' => 'SENHA_EMITIDA'],
                ],
            ], 200),
        ]);
    }

    #[Test]
    public function calcula_resumo_corretamente(): void
    {
        Cache::forget('novosga_token');
        $this->fakeNovosga();

        $resumo = $this->service()->resumo();

        $this->assertSame(3, $resumo['total_dia']);
        $this->assertSame(2, $resumo['em_fila']);
        $this->assertSame(1, $resumo['em_atendimento']);
        $this->assertSame(450, $resumo['tempo_medio_espera_s']);
        $this->assertSame(600, $resumo['tempo_medio_atendimento_s']);
        $this->assertSame(1, $resumo['nao_compareceu']);
    }

    #[Test]
    public function agrega_por_setor_e_status(): void
    {
        Cache::forget('novosga_token');
        $this->fakeNovosga();

        $setores = $this->service()->porSetor();
        $this->assertSame('Atendimento Geral', $setores[0]['setor']);
        $this->assertSame(2, $setores[0]['total']);

        $status = $this->service()->porStatus();
        $mapa = collect($status)->pluck('total', 'status')->all();
        $this->assertSame(2, $mapa['FINALIZADO']);
        $this->assertSame(1, $mapa['EM_ATENDIMENTO']);
        $this->assertSame(1, $mapa['NAO_COMPARECEU']);
    }

    #[Test]
    public function busca_por_termo(): void
    {
        Cache::forget('novosga_token');
        $this->fakeNovosga();

        $resultados = $this->service()->buscar('gabinete');

        $this->assertCount(1, $resultados);
        $this->assertSame(2, $resultados[0]['id']);
    }

    #[Test]
    public function sem_simulacao_devolve_vazio_quando_indisponivel(): void
    {
        Cache::forget('novosga_token');

        Http::fake([
            'http://novosga.test/api/token' => Http::response([], 503),
        ]);

        $this->assertSame([], $this->service(simulation: false)->atendimentos());
    }

    #[Test]
    public function com_simulacao_devolve_demonstracao_quando_indisponivel(): void
    {
        Cache::forget('novosga_token');

        Http::fake([
            'http://novosga.test/api/token' => Http::response([], 503),
        ]);

        $this->assertNotEmpty($this->service(simulation: true)->atendimentos());
    }
}
