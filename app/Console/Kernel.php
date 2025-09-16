<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendReminders::class,
        \App\Console\Commands\TestReminders::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Enviar recordatorios cada minuto (en producción podrías usar ->daily() o ->hourly())
        $schedule->command('reminders:send')->everyMinute();
        
        // Limpiar tokens expirados semanalmente
        $schedule->command('sanctum:prune-expired --hours=24')->weekly();
        
        // Opcional: limpiar recordatorios enviados antiguos (más de 30 días)
        $schedule->command('model:prune', [
            '--model' => 'App\Models\Recordatorio',
            '--except' => ['enviado', false]
        ])->monthly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}