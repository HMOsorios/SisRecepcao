<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Bi\BiController;
use App\Http\Controllers\Configuracoes\ConfiguracoesController;
use App\Http\Controllers\Diretoria\DiretoriaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Legal\LegalController;
use App\Http\Controllers\SaudeController;
use App\Http\Controllers\Usuarios\UsuariosController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SisRecepção — Admin (portal institucional)
|--------------------------------------------------------------------------
| Portal (8.5), Diretoria (8.1), BI (2.5), Usuários (8.3), Configurações (8.4),
| páginas legais (8.7).
*/

Route::get('/health', SaudeController::class)->name('health');

// ---- Página inicial pública e redirecionamento por perfil (8.5) ----
Route::get('/', [HomeController::class, 'index'])->name('home.index');
Route::get('/inicio', [HomeController::class, 'dashboard'])->name('home.dashboard');

// ---- Páginas legais (8.7) ----
Route::prefix('legal')->name('legal.')->group(function (): void {
    Route::get('/termos', [LegalController::class, 'termos'])->name('termos');
    Route::get('/privacidade', [LegalController::class, 'privacidade'])->name('privacidade');
    Route::get('/lgpd', [LegalController::class, 'lgpd'])->name('lgpd');
});

// ---- Autenticação (Keycloak — 8.6) ----
Route::prefix('auth')->name('auth.')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/login/iniciar', [AuthController::class, 'iniciar'])->name('login.iniciar');
    Route::get('/callback', [AuthController::class, 'callback'])->name('callback');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/sair', [AuthController::class, 'sair'])->name('sair');
});

// ---- Área autenticada ----
Route::middleware('auth.keycloak')->group(function (): void {
    // BI (Servidor e superiores) — 2.5
    Route::middleware('perfil:Developer,Admin,Servidor')->prefix('bi')->name('bi.')->group(function (): void {
        Route::get('/', fn () => redirect()->route('bi.resumo'))->name('index');
        Route::get('/resumo', fn () => view('bi.index'))->name('resumo');
        Route::get('/api/resumo', [BiController::class, 'resumo'])->name('api.resumo');
        Route::get('/api/setores', [BiController::class, 'setores'])->name('api.setores');
        Route::get('/api/status', [BiController::class, 'status'])->name('api.status');
        Route::get('/api/buscar', [BiController::class, 'buscar'])->name('api.buscar');
    });

    // Painel da Diretoria (8.1) — Developer/Admin/Diretoria
    Route::middleware('perfil:Developer,Admin,Diretoria')->prefix('diretoria')->name('diretoria.')->group(function (): void {
        Route::get('/', [DiretoriaController::class, 'index'])->name('index');
        Route::get('/relatorio', [DiretoriaController::class, 'relatorio'])->name('relatorio');
    });

    // Usuários (8.3) — Admin e Developer
    Route::middleware('perfil:Developer,Admin')->prefix('usuarios')->name('usuarios.')->group(function (): void {
        Route::get('/', [UsuariosController::class, 'index'])->name('index');
    });

    // Configurações e Manutenção (8.4) — somente Developer
    Route::middleware('perfil:Developer')->prefix('configuracoes')->name('configuracoes.')->group(function (): void {
        Route::get('/', [ConfiguracoesController::class, 'index'])->name('index');
        Route::post('/salvar', [ConfiguracoesController::class, 'salvar'])->name('salvar');
        Route::post('/acao/{acao}', [ConfiguracoesController::class, 'acao'])->name('acao');
    });
});
