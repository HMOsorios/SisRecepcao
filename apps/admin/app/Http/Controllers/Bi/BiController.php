<?php

namespace App\Http\Controllers\Bi;

use App\Http\Controllers\Controller;
use App\Services\Bi\BiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoints JSON do módulo de BI (Seção 2.5) — buscas em tempo real.
 */
class BiController extends Controller
{
    public function __construct(protected readonly BiService $bi) {}

    public function resumo(): JsonResponse
    {
        return response()->json($this->bi->resumo());
    }

    public function setores(): JsonResponse
    {
        return response()->json($this->bi->porSetor());
    }

    public function status(): JsonResponse
    {
        return response()->json($this->bi->porStatus());
    }

    public function buscar(Request $request): JsonResponse
    {
        return response()->json([
            'resultados' => $this->bi->buscar((string) $request->query('q', ''), (int) $request->query('max', 30)),
        ]);
    }
}
