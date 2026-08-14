<?php

namespace Tests\Feature;

use App\Models\Configuracao;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\AutenticaKeycloak;
use Tests\TestCase;

class ConfiguracoesTest extends TestCase
{
    use AutenticaKeycloak;
    use RefreshDatabase;

    #[Test]
    public function apenas_developer_acessa(): void
    {
        $this->autenticar('Admin');
        $this->get('/configuracoes')->assertForbidden();

        $this->autenticar('Developer');
        $this->get('/configuracoes')->assertOk()->assertSee('Configurações e Manutenção');
    }

    #[Test]
    public function salvar_configuracoes_persiste_e_exibe_mensagem(): void
    {
        $this->autenticar('Developer');

        $this->post('/configuracoes/salvar', [
            'campos' => [
                'sms_nome' => 'SMS de Teste',
                'sms_sigla' => 'SMS-T',
                'sms_endereco' => '',
                'sms_telefone' => '',
                'sms_email' => '',
                'sms_identidade' => '',
                'sis_pec_url' => '',
                'sei_url' => '',
                'ti_conecta_url' => '',
                'sis_escala_url' => '',
            ],
        ])
            ->assertRedirect()
            ->assertSessionHas('ok');

        $this->assertSame('SMS de Teste', Configuracao::get('sms_nome'));
        $this->assertDatabaseCount('log_auditorias', 1);
    }

    #[Test]
    public function acao_invalida_da_404(): void
    {
        $this->autenticar('Developer');

        $this->post('/configuracoes/acao/nao_existe')->assertNotFound();
    }

    #[Test]
    public function acao_deploy_coolify_sem_webhook_falha_amigavel(): void
    {
        config()->set('sms.coolify_webhook_url', '');
        $this->autenticar('Developer');

        $this->post('/configuracoes/acao/deploy_coolify')
            ->assertRedirect()
            ->assertSessionHas('erro');

        $this->assertDatabaseCount('log_auditorias', 1);
    }

    #[Test]
    public function acao_deploy_coolify_chama_webhook(): void
    {
        config()->set('sms.coolify_webhook_url', 'https://coolify.exemplo/hook');

        Http::fake([
            'https://coolify.exemplo/hook' => Http::response([], 200),
        ]);

        $this->autenticar('Developer');

        $this->post('/configuracoes/acao/deploy_coolify')
            ->assertRedirect()
            ->assertSessionHas('ok');
    }
}
