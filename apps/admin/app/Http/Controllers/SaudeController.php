<?php

namespace App\Http\Controllers;

use App\Services\Novosga\NovosgaClient;
use Illuminate\Http\JsonResponse;

/**
 * Health check pós-deploy (Seção 9.4) — expõe versão e conectividade.
 */
class SaudeController extends Controller
{
    public function __invoke(NovosgaClient $client): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'app' => 'sisrecepcao-admin',
            'versao' => config('app.version', '1.0.0'),
            'novosga' => $client->saudavel(),
            'agora' => now()->toIso8601String(),
        ]);
    }
}
