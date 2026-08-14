<?php

namespace Tests\Feature;

use App\Models\Cracha;
use App\Models\Visitante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CrachaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // O módulo de crachá é operado pelo recepcionista, não é self-service
        // público — exige login (Seção 8.8).
        Session::put(config('keycloak.session_user_key'), [
            'sub' => 'user-1',
            'preferred_username' => 'recepcao',
            'name' => 'Recepção',
        ]);
    }

    public function test_form_renderiza_campos(): void
    {
        $response = $this->get(route('cracha.form'));

        $response->assertOk();
        $response->assertSee('Nome completo');
        $response->assertSee('Documento');
    }

    public function test_registrar_visitante_e_emitir_cracha(): void
    {
        $response = $this->post(route('cracha.registrar'), [
            'nome' => 'João da Silva',
            'documento' => '123.456.789-01',
            'tipo_documento' => 'CPF',
            'setor_destino' => 'Gabinete',
            'sala_destino' => 'Sala 204',
        ]);

        $this->assertDatabaseHas('visitantes', [
            'nome' => 'João da Silva',
            'documento' => '12345678901',
        ]);

        $visitante = Visitante::query()->first();
        $cracha = Cracha::query()->first();

        $this->assertNotNull($cracha);
        $this->assertSame($visitante->id, $cracha->visitante_id);
        $this->assertSame(Cracha::STATUS_ATIVO, $cracha->status);

        $response->assertRedirect(route('cracha.imprimir', $cracha));
    }

    public function test_registrar_requer_dados_minimos(): void
    {
        $response = $this->post(route('cracha.registrar'), [
            'nome' => '',
            'documento' => '',
        ]);

        $response->assertSessionHasErrors(['nome', 'documento']);
        $this->assertDatabaseCount('visitantes', 0);
    }

    public function test_impressao_mostra_cpf_mascarado(): void
    {
        $visitante = Visitante::create([
            'nome' => 'João da Silva',
            'documento' => '12345678901',
        ]);

        $cracha = Cracha::create([
            'visitante_id' => $visitante->id,
            'codigo' => 'ABC123',
            'status' => Cracha::STATUS_ATIVO,
            'emitido_em' => now(),
            'expira_em' => now()->addMinutes(120),
        ]);

        $response = $this->get(route('cracha.imprimir', $cracha));

        $response->assertOk();
        $response->assertSee('123.***.***-01');
        $response->assertSee('ABC123');
        $response->assertDontSee('12345678901');
    }

    public function test_devolver_invalida_o_cracha(): void
    {
        $visitante = Visitante::create(['nome' => 'João', 'documento' => '12345678901']);
        $cracha = Cracha::create([
            'visitante_id' => $visitante->id,
            'codigo' => 'ABC123',
            'status' => Cracha::STATUS_ATIVO,
            'emitido_em' => now(),
            'expira_em' => now()->addMinutes(120),
        ]);

        $response = $this->post(route('cracha.devolver', $cracha));

        $response->assertSessionHas('ok');
        $this->assertSame(Cracha::STATUS_INATIVO, $cracha->fresh()->status);
        $this->assertNotNull($cracha->fresh()->devolvido_em);
    }
}
