<?php

namespace App\Http\Controllers;

use App\Services\Novosga\NovosgaClient;
use Illuminate\Http\JsonResponse;

/**
 * Health check pós-deploy (Seção 9.4) e verificação de contingência.
 */
class SaudeController extends Controller
{
    public function __invoke(NovosgaClient $client): JsonResponse
    {
        $novosga = $client->saudavel();

        return response()->json([
            'app' => 'ok',
            'versao' => config('app.version', '1.0.0'),
            'data' => now()->toIso8601String(),
            'novosga' => $novosga ? 'online' : 'offline',
            'modo' => $novosga ? 'normal' : 'contingencia',
        ]);
    }
}
