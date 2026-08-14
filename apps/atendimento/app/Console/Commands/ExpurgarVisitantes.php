<?php

namespace App\Console\Commands;

use App\Models\Cracha;
use App\Models\Visitante;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

/**
 * Expurgo automatizado de dados de visitantes — LGPD (Seção 2.5/9.6).
 *
 * Remove (ou anonimiza) dados pessoais de visitantes após o prazo de retenção
 * configurável (sms.retencao_visitantes_dias). Fotos armazenadas também são
 * apagadas. Parâmetro: --anonimize preserva contagem estatística anonimizada.
 */
class ExpurgarVisitantes extends Command
{
    protected $signature = 'sisrecepcao:expurgar-visitantes {--anonimize : mantém registro anônimo para BI}';

    protected $description = 'Expurga dados pessoais de visitantes vencidos (LGPD)';

    public function handle(): int
    {
        $prazoDias = (int) config('sms.retencao_visitantes_dias', 30);
        $limite = now()->subDays($prazoDias);

        $visitantes = Visitante::query()
            ->where('created_at', '<', $limite)
            ->whereNull('purged_at')
            ->limit(500)
            ->get();

        $contagem = 0;

        // Um único DELETE em lote para os crachás, em vez de um por visitante
        // dentro do loop — a rotina de expurgo deve ser leve mesmo com o
        // limite de 500 registros por execução.
        Cracha::query()->whereIn('visitante_id', $visitantes->pluck('id'))->delete();

        foreach ($visitantes as $visitante) {
            if ($visitante->foto) {
                Storage::disk('public')->delete($visitante->foto);
            }

            if ($this->option('anonimize')) {
                $visitante->update([
                    'nome' => '[anonimizado]',
                    'documento' => 'ANON-'.$visitante->id,
                    'email' => null,
                    'telefone' => null,
                    'foto' => null,
                    'observacao' => null,
                    'purged_at' => now(),
                ]);
            } else {
                $visitante->delete();
            }

            $contagem++;
        }

        $this->info("Dados de {$contagem} visitante(s) expurgados.");

        return self::SUCCESS;
    }
}
