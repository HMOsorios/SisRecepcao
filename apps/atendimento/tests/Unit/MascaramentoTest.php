<?php

namespace Tests\Unit;

use App\Models\Mascaramento;
use PHPUnit\Framework\TestCase;

class MascaramentoTest extends TestCase
{
    public function test_cpf_com_11_digitos_e_mascarado(): void
    {
        $this->assertSame('123.***.***-01', Mascaramento::cpf('12345678901'));
    }

    public function test_cpf_invalido_usa_mascaramento_generico(): void
    {
        $this->assertSame('A***E', Mascaramento::cpf('ABCDE'));
    }

    public function test_telefone_celular_e_mascarado(): void
    {
        $this->assertSame('(11) 9****-**21', Mascaramento::telefone('11987654321'));
    }

    public function test_telefone_fixo_e_mascarado(): void
    {
        $this->assertSame('(11) ****-**90', Mascaramento::telefone('1134567890'));
    }
}
