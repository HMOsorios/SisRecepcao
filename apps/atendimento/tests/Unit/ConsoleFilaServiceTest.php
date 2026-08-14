<?php

namespace Tests\Unit;

use App\Services\Fila\ConsoleFilaService;
use PHPUnit\Framework\TestCase;

class ConsoleFilaServiceTest extends TestCase
{
    private ConsoleFilaService $servico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servico = new ConsoleFilaService;
    }

    public function test_escolhe_prioridade_legal_primeiro(): void
    {
        $fila = [
            ['id' => 1, 'status' => 'SENHA_EMITIDA', 'dataChegada' => '2026-08-14T10:00:00', 'prioridade' => ['peso' => 0]],
            ['id' => 2, 'status' => 'SENHA_EMITIDA', 'dataChegada' => '2026-08-14T10:05:00', 'prioridade' => ['peso' => 100]],
        ];

        $proxima = $this->servico->proximaSenha($fila);

        $this->assertSame(2, $proxima['id']);
    }

    public function test_empate_usa_ordem_de_chegada(): void
    {
        $fila = [
            ['id' => 1, 'status' => 'SENHA_EMITIDA', 'dataChegada' => '2026-08-14T10:10:00', 'prioridade' => ['peso' => 100]],
            ['id' => 2, 'status' => 'SENHA_EMITIDA', 'dataChegada' => '2026-08-14T10:05:00', 'prioridade' => ['peso' => 100]],
        ];

        $proxima = $this->servico->proximaSenha($fila);

        $this->assertSame(2, $proxima['id']);
    }

    public function test_ignora_senhas_ja_chamadas(): void
    {
        $fila = [
            ['id' => 1, 'status' => 'CHAMADO', 'prioridade' => ['peso' => 100]],
            ['id' => 2, 'status' => 'SENHA_EMITIDA', 'prioridade' => ['peso' => 0]],
        ];

        $proxima = $this->servico->proximaSenha($fila);

        $this->assertSame(2, $proxima['id']);
    }

    public function test_fila_vazia_retorna_null(): void
    {
        $this->assertNull($this->servico->proximaSenha([]));
    }
}
