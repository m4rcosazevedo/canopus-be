<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Executa no primeiro dia de cada mês à meia-noite
Schedule::command('audit:backup-clean')->monthly();
//Schedule::command('audit:backup-clean')->everyMinute()->withoutOverlapping();
//Schedule::command(\App\Console\Commands\BackupAndPruneAuditLogs::class)->monthly(); pode rodar assim também

/*
  Por que usar withoutOverlapping()?
    Ao rodar a cada minuto, existe o risco de um processo de exportação de CSV ainda não ter terminado quando o próximo minuto chegar
    (especialmente se houver muitos dados para processar).
    O método withoutOverlapping() cria um "trava" (lock). Se o comando do minuto anterior ainda estiver rodando, o Laravel pula a execução
    atual e espera a próxima janela, evitando que vários processos tentem escrever no mesmo arquivo CSV ou travem o banco de dados.
*/
