<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Agendamento do SisRecepção — Atendimento
|--------------------------------------------------------------------------
| Reconciliação de senhas provisórias do outbox (Seção 9.1) e expurgo de
| dados de visitantes conforme LGPD (Seção 2.5/9.6).
*/

Schedule::command('sisrecepcao:reconciliar-senhas')->everyFiveMinutes()->withoutOverlapping();

Schedule::command('sisrecepcao:expurgar-visitantes')
    ->dailyAt('02:30')
    ->withoutOverlapping();

Artisan::command('inspire', function () {
    $this->comment('SisRecepção — Atendimento no ar.');
})->purpose('Display an inspiring quote');
