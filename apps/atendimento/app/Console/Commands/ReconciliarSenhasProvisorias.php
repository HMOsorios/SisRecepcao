<?php

namespace App\Console\Commands;

use App\Services\Fila\EmissaoSenhaService;
use Illuminate\Console\Command;

/**
 * Reconcilia senhas provisórias do outbox com o NovoSGA (Seção 9.1).
 *
 * O totem emite senha local quando o NovoSGA está indisponível; este comando
 * reenvia via POST /api/distribui assim que a conectividade volta, preservando
 * a ordem real de chegada. Agendar via scheduler (routes/console.php).
 */
class ReconciliarSenhasProvisorias extends Command
{
    protected $signature = 'sisrecepcao:reconciliar-senhas';

    protected $description = 'Reenvia senhas provisórias do outbox para o NovoSGA';

    public function handle(EmissaoSenhaService $servico): int
    {
        $enviadas = $servico->reenviarPendentes();

        $this->info("Senhas provisórias reconciliadas: {$enviadas}.");

        return self::SUCCESS;
    }
}
