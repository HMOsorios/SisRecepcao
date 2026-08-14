<?php

namespace Tests\Unit;

use App\Models\SenhaOutbox;
use App\Services\Fila\EmissaoSenhaService;
use App\Services\Novosga\NovosgaClient;
use App\Services\Novosga\NovosgaIndisponivelException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmissaoSenhaServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_emitir_em_simulacao_grava_outbox_provisorio(): void
    {
        $client = $this->createMock(NovosgaClient::class);
        $client->expects($this->never())->method('distribuiSenha');

        $servico = new EmissaoSenhaService($client, 1, simulation: true);

        $resultado = $servico->emitir(1, 2, ['nome' => 'Maria'], ['totem' => true]);

        $this->assertTrue($resultado['provisional']);
        $this->assertInstanceOf(SenhaOutbox::class, $resultado['outbox']);
        $this->assertStringStartsWith('OFF-', $resultado['outbox']->senha_provisoria);
        $this->assertSame(SenhaOutbox::STATUS_PENDENTE, $resultado['outbox']->status);
        $this->assertSame(1, $resultado['outbox']->servico_id);
        $this->assertSame(2, $resultado['outbox']->prioridade_id);
    }

    public function test_emitir_quando_novosga_indisponivel_usa_outbox(): void
    {
        $client = $this->createMock(NovosgaClient::class);
        $client->method('distribuiSenha')->willThrowException(new NovosgaIndisponivelException('timeout', 0));

        $servico = new EmissaoSenhaService($client, 1, simulation: false);

        $resultado = $servico->emitir(1, 1);

        $this->assertTrue($resultado['provisional']);
        $this->assertDatabaseHas('senha_outbox', ['servico_id' => 1]);
    }

    public function test_emitir_com_novosga_disponivel_nao_usa_outbox(): void
    {
        $client = $this->createMock(NovosgaClient::class);
        $client->method('distribuiSenha')->willReturn([
            'id' => 42,
            'senha' => ['sigla' => 'AG', 'numero' => 7],
        ]);

        $servico = new EmissaoSenhaService($client, 1, simulation: false);

        $resultado = $servico->emitir(1, 1);

        $this->assertFalse($resultado['provisional']);
        $this->assertSame(42, $resultado['atendimento']['id']);
        $this->assertDatabaseCount('senha_outbox', 0);
    }

    public function test_reenviar_pendentes_marca_como_enviado(): void
    {
        $outbox = SenhaOutbox::create([
            'senha_provisoria' => 'OFF-0001',
            'unidade_id' => 1,
            'servico_id' => 1,
            'prioridade_id' => 1,
            'status' => SenhaOutbox::STATUS_PENDENTE,
            'data_chegada' => now(),
        ]);

        $client = $this->createMock(NovosgaClient::class);
        $client->expects($this->once())->method('distribuiSenha')->willReturn(['id' => 99]);

        $servico = new EmissaoSenhaService($client, 1, simulation: false);

        $enviadas = $servico->reenviarPendentes();

        $this->assertSame(1, $enviadas);
        $this->assertDatabaseHas('senha_outbox', [
            'id' => $outbox->id,
            'status' => SenhaOutbox::STATUS_ENVIADO,
        ]);
        $this->assertNotNull($outbox->fresh()->enviado_em);
    }

    public function test_reenviar_pendentes_em_simulacao_nao_envia(): void
    {
        SenhaOutbox::create([
            'senha_provisoria' => 'OFF-0001',
            'unidade_id' => 1,
            'servico_id' => 1,
            'prioridade_id' => 1,
            'status' => SenhaOutbox::STATUS_PENDENTE,
            'data_chegada' => now(),
        ]);

        $client = $this->createMock(NovosgaClient::class);
        $client->expects($this->never())->method('distribuiSenha');

        $servico = new EmissaoSenhaService($client, 1, simulation: true);

        $this->assertSame(0, $servico->reenviarPendentes());
    }
}
