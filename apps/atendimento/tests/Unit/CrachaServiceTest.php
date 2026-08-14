<?php

namespace Tests\Unit;

use App\Models\Cracha;
use App\Models\Visitante;
use App\Services\Cracha\CrachaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrachaServiceTest extends TestCase
{
    use RefreshDatabase;

    private CrachaService $servico;

    protected function setUp(): void
    {
        parent::setUp();

        $this->servico = new CrachaService;
    }

    public function test_registrar_normaliza_documento_para_digitos(): void
    {
        $visitante = $this->servico->registrar([
            'nome' => 'João Silva',
            'documento' => '123.456.789-01',
        ]);

        $this->assertSame('12345678901', $visitante->documento);
    }

    public function test_emitir_cracha_define_codigo_e_validade(): void
    {
        $visitante = Visitante::create(['nome' => 'João', 'documento' => '12345678901']);

        $cracha = $this->servico->emitirCracha($visitante, 120);

        $this->assertSame(Cracha::STATUS_ATIVO, $cracha->status);
        $this->assertSame(6, strlen($cracha->codigo));
        $this->assertNotNull($cracha->emitido_em);
        $this->assertNotNull($cracha->expira_em);
    }

    public function test_codigo_do_cracha_e_unico(): void
    {
        $visitante = Visitante::create(['nome' => 'João', 'documento' => '12345678901']);

        $codigos = [];
        for ($i = 0; $i < 5; $i++) {
            $codigos[] = $this->servico->emitirCracha($visitante)->codigo;
        }

        $this->assertCount(5, array_unique($codigos));
    }

    public function test_devolver_cracha_invalida(): void
    {
        $visitante = Visitante::create(['nome' => 'João', 'documento' => '12345678901']);
        $cracha = $this->servico->emitirCracha($visitante);

        $this->servico->devolverCracha($cracha);

        $this->assertSame(Cracha::STATUS_INATIVO, $cracha->fresh()->status);
        $this->assertNotNull($cracha->fresh()->devolvido_em);
    }

    public function test_cpf_mascarado_para_cracha(): void
    {
        $this->assertSame('123.***.***-01', $this->servico->cpfMascarado('12345678901'));
    }
}
