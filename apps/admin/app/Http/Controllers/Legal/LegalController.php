<?php

namespace App\Http\Controllers\Legal;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Páginas legais (Seção 8.7): Termos de Uso, Política de Privacidade e LGPD.
 */
class LegalController extends Controller
{
    public function termos(): View
    {
        return view('legal.termos', ['atualizadoEm' => now()->startOfMonth()]);
    }

    public function privacidade(): View
    {
        return view('legal.privacidade', ['atualizadoEm' => now()->startOfMonth()]);
    }

    public function lgpd(): View
    {
        return view('legal.lgpd', ['atualizadoEm' => now()->startOfMonth()]);
    }
}
