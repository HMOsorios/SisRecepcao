<?php

namespace Tests\Unit;

use App\Services\Novosga\NovosgaApiException;
use App\Services\Novosga\NovosgaClient;
use App\Services\Novosga\NovosgaIndisponivelException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NovosgaClientTest extends TestCase
{
    protected function cliente(): NovosgaClient
    {
        return new NovosgaClient(
            baseUrl: 'http://novosga.test',
            clientId: 'sisrecepcao_admin',
            clientSecret: 'segredo',
            username: '',
            password: '',
            timeout: 3,
            retries: 1,
        );
    }

    #[Test]
    public function obtem_token_e_cacheia(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok-1'], 200),
            'http://novosga.test/api/unidades' => Http::response(['data' => [['id' => 1, 'nome' => 'Sede']]], 200),
        ]);

        $cliente = $this->cliente();

        $this->assertSame('tok-1', $cliente->token());
        $this->assertSame('tok-1', $cliente->token());

        Http::assertSentCount(1);

        $this->assertSame([['id' => 1, 'nome' => 'Sede']], $cliente->unidades());
    }

    #[Test]
    public function usa_grant_password_quando_sem_secret(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok-2'], 200),
        ]);

        $cliente = new NovosgaClient('http://novosga.test', 'x', '', 'user', 'senha', 3, 1);
        $this->assertSame('tok-2', $cliente->token());

        Http::assertSent(fn ($request) => $request->url() === 'http://novosga.test/api/token'
            && $request['grant_type'] === 'password'
            && $request['username'] === 'user');
    }

    #[Test]
    public function lança_api_exception_em_4xx(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response(['error' => 'invalid_client'], 401),
        ]);

        $this->expectException(NovosgaApiException::class);
        $this->expectExceptionCode(401);

        $this->cliente()->token();
    }

    #[Test]
    public function lança_indisponivel_em_5xx(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response([], 503),
        ]);

        $this->expectException(NovosgaIndisponivelException::class);

        $this->cliente()->token();
    }

    #[Test]
    public function saudavel_false_quando_indisponivel(): void
    {
        Http::fake([
            'http://novosga.test/api/token' => Http::response([], 503),
        ]);

        $this->assertFalse($this->cliente()->saudavel());
    }

    #[Test]
    public function invalida_cache_do_token_entre_instancias(): void
    {
        Cache::forget('novosga_token');

        Http::fake([
            'http://novosga.test/api/token' => Http::response(['access_token' => 'tok-x'], 200),
        ]);

        $this->assertSame('tok-x', $this->cliente()->token());
    }
}
