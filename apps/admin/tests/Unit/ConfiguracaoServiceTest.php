<?php

namespace Tests\Unit;

use App\Models\Configuracao;
use App\Services\Configuracoes\ConfiguracaoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfiguracaoServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function service(): ConfiguracaoService
    {
        return $this->app->make(ConfiguracaoService::class);
    }

    #[Test]
    public function valores_usam_padrao_de_config_quando_nao_gravados(): void
    {
        $valores = $this->service()->valores();

        $this->assertSame(config('sms.nome'), $valores['sms_nome']);
        $this->assertSame(config('sms.sigla'), $valores['sms_sigla']);
    }

    #[Test]
    public function salvar_persiste_e_audita(): void
    {
        $service = $this->service();

        $service->salvar([
            'sms_nome' => 'Secretaria Municipal de Saúde de Teste',
            'sms_sigla' => 'SMS-T',
            'sms_endereco' => 'Rua das Flores, 100',
        ], 'user-1', 'Gestor', Request::create('/', 'POST'));

        $this->assertSame('Secretaria Municipal de Saúde de Teste', Configuracao::get('sms_nome'));
        $this->assertDatabaseHas('log_auditorias', [
            'usuario_id' => 'user-1',
            'usuario_nome' => 'Gestor',
            'acao' => 'configuracoes.salvar',
        ]);

        $valores = $service->valores();
        $this->assertSame('SMS-T', $valores['sms_sigla']);
        $this->assertSame('Rua das Flores, 100', $valores['sms_endereco']);
    }

    #[Test]
    public function campos_contem_todos_os_editaveis(): void
    {
        $campos = $this->service()->campos();

        foreach (['sms_nome', 'sms_sigla', 'sms_endereco', 'sei_url', 'sis_escala_url'] as $chave) {
            $this->assertArrayHasKey($chave, $campos);
        }
    }
}
