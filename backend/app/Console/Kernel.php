<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan fornecidos pela aplicação.
     *
     * @var array
     */
    protected $commands = [
        // Adicione seu comando personalizado aqui
        \App\Console\Commands\CustomRouteList::class,
    ];

    /**
     * Define o agendamento de comandos da aplicação.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Registra os comandos para a aplicação.
     *
     * @return void
     */
    protected function commands()
    {
        // No Laravel 5.4, não temos o método load
        // Os comandos são registrados no array $commands acima
        
        // Se você tiver um arquivo routes/console.php, descomente a linha abaixo:
        // require base_path('routes/console.php');
    }
}