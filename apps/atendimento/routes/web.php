<?php

use App\Http\Controllers\Atendente\AtendenteController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Cracha\CrachaController;
use App\Http\Controllers\PainelTv\PainelTvController;
use App\Http\Controllers\SaudeController;
use App\Http\Controllers\Totem\TotemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SisRecepção — Atendimento (linha de frente)
|--------------------------------------------------------------------------
| Totem (2.1), Painel TV (2.2), Crachá (2.3), Console do atendente (2.4).
*/

// Saúde / health check pós-deploy (Seção 9.4)
Route::get('/health', SaudeController::class)->name('health');

Route::get('/', fn () => redirect()->route('totem.index'));

// ---- Autenticação (Keycloak — Seção 5.4/8.6) ----
Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/callback', [AuthController::class, 'callback'])->name('callback');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/sair', [AuthController::class, 'sair'])->name('sair');
});

// ---- Módulo 2.1 — Totem de autoatendimento (PWA kiosk) ----
Route::prefix('totem')->name('totem.')->group(function (): void {
    Route::get('/', [TotemController::class, 'index'])->name('index');
    Route::get('/dados', [TotemController::class, 'dados'])->name('dados');
    Route::post('/emitir', [TotemController::class, 'emitir'])
        ->middleware(['honeypot', 'throttle:'.config('totem.rate_limit').',1'])
        ->name('emitir');
    Route::get('/ticket/{id}', [TotemController::class, 'ticket'])->name('ticket');
});

// ---- Módulo 2.2 — Painel de chamada (Smart TV) ----
Route::prefix('painel')->name('painel.')->group(function (): void {
    Route::get('/', [PainelTvController::class, 'index'])->name('index');
    Route::get('/dados', [PainelTvController::class, 'dados'])->name('dados');
});

// ---- Módulo 2.3 — Crachás e controle de acesso ----
// Operado pelo recepcionista (não é self-service público, ao contrário do
// totem) — exige login e limita a taxa de submissão (Seção 8.8).
Route::prefix('cracha')->name('cracha.')->middleware(['auth.keycloak'])->group(function (): void {
    Route::get('/', [CrachaController::class, 'form'])->name('form');
    Route::post('/', [CrachaController::class, 'registrar'])
        ->middleware(['throttle:30,1'])
        ->name('registrar');
    Route::get('/{cracha}/imprimir', [CrachaController::class, 'imprimir'])->name('imprimir');
    Route::post('/{cracha}/devolver', [CrachaController::class, 'devolver'])->name('devolver');
});

// ---- Módulo 2.4 — Console do atendente (autenticado via Keycloak) ----
Route::prefix('atendente')->name('atendente.')->middleware(['auth.keycloak'])->group(function (): void {
    Route::get('/', [AtendenteController::class, 'index'])->name('index');
    Route::get('/fila', [AtendenteController::class, 'fila'])->name('fila');
    Route::post('/proxima', [AtendenteController::class, 'proxima'])->name('proxima');
    Route::post('/chamar', [AtendenteController::class, 'chamar'])->name('chamar');
    Route::post('/iniciar/{id}', [AtendenteController::class, 'iniciar'])->name('iniciar');
    Route::post('/encerrar/{id}', [AtendenteController::class, 'encerrar'])->name('encerrar');
    Route::post('/redirecionar/{id}', [AtendenteController::class, 'redirecionar'])->name('redirecionar');
    Route::post('/status', [AtendenteController::class, 'status'])->name('status');
    Route::get('/notificacoes/{departamentoId}', [AtendenteController::class, 'notificacoes'])->name('notificacoes');
    Route::post('/notificacoes/{id}/lida', [AtendenteController::class, 'marcarNotificacao'])->name('notificacao.lida');
});
