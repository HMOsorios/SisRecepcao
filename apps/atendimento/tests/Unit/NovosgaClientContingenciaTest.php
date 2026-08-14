<?php

namespace Tests\Unit;

use App\Services\Novosga\NovosgaClient;
use App\Services\Novosga\NovosgaIndisponivelException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Regressão da Seção 9.1: uma queda real do NovoSGA (timeout/conexão
 * recusada) precisa cair no mesmo fallback de contingência que um HTTP 5xx —
 * antes da correção, ConnectionException escapava sem virar
 * NovosgaIndisponivelException e o outbox nunca era acionado.
 */
class NovosgaClientContingenciaTest extends TestCase
{
    protected function cliente(): NovosgaClient
    {
        return new NovosgaClient(
            baseUrl: 'http://novosga.test',
            clientId: 'sisrecepcao_atendimento',
            clientSecret: 'segredo',
            username: '',
            password: '',
            timeout: 3,
            retries: 1,
        );
    }

    #[Test]
    public function conexao_recusada_no_distribui_vira_indisponivel(): void
    {
        Cache::forget('novosga_token');

        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok'], 200),
            'http://novosga.test/api/distribui' => fn () => throw new ConnectionException('Connection refused'),
        ]);

        $this->expectException(NovosgaIndisponivelException::class);

        $this->cliente()->distribuiSenha(1, 1, 1);
    }

    #[Test]
    public function conexao_recusada_no_token_vira_indisponivel(): void
    {
        Cache::forget('novosga_token');

        Http::fake([
            'http://novosga.test/api/token' => fn () => throw new ConnectionException('Connection refused'),
        ]);

        $this->expectException(NovosgaIndisponivelException::class);

        $this->cliente()->token();
    }

    #[Test]
    public function resposta_401_invalida_o_token_em_cache_e_vira_indisponivel(): void
    {
        Cache::forget('novosga_token');
        Cache::put('novosga_token', 'token-velho', 3600);

        Http::fake([
            'http://novosga.test/api/atendimentos/1' => Http::response(['error' => 'token expirado'], 401),
        ]);

        try {
            $this->cliente()->atendimento(1);
            $this->fail('Deveria ter lançado NovosgaIndisponivelException.');
        } catch (NovosgaIndisponivelException) {
            // esperado
        }

        $this->assertNull(Cache::get('novosga_token'));
    }
}
