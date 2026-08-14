<?php

namespace App\Services\Fila;

/**
 * Regras de ordenação/alternância do console do atendente (Módulo 2.4).
 *
 * A ordenação com "aging" (evita fome de senha convencional) é responsabilidade
 * do NovoSGA (Seção 5.1). Aqui apenas garantimos que a escolha da "próxima
 * senha" do console respeite as prioridades legais (idoso, gestante, PCD, TEA).
 */
class ConsoleFilaService
{
    /**
     * Escolhe a próxima senha a chamar a partir da fila retornada pelo NovoSGA.
     *
     * Regra: prioridade com maior peso primeiro; em empate, quem chegou antes.
     *
     * @param  array<int,array<string,mixed>>  $fila
     * @return array<string,mixed>|null
     */
    public function proximaSenha(array $fila): ?array
    {
        $fila = array_values(array_filter($fila, fn (array $t) => $this->chamavel($t)));

        if ($fila === []) {
            return null;
        }

        usort($fila, function (array $a, array $b): int {
            $pesoA = (int) ($a['prioridade']['peso'] ?? 0);
            $pesoB = (int) ($b['prioridade']['peso'] ?? 0);

            if ($pesoA !== $pesoB) {
                return $pesoB <=> $pesoA;
            }

            return ($a['dataChegada'] ?? '') <=> ($b['dataChegada'] ?? '');
        });

        return $fila[0];
    }

    /**
     * Indica se a senha ainda pode ser chamada.
     */
    protected function chamavel(array $ticket): bool
    {
        $status = (string) ($ticket['status'] ?? '');

        return in_array($status, ['SENHA_EMITIDA', 'NAO_COMPARECEU'], true);
    }
}
