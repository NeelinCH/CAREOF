<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recordatorio;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskReminder;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send task reminders to users';

    public function handle()
    {
        $now = now();
        
        // Obtener recordatorios pendientes
        $reminders = Recordatorio::with('tarea.planta.user')
            ->where('fecha_envio', '<=', $now)
            ->where('enviado', false)
            ->get();
        
        foreach ($reminders as $reminder) {
            $user = $reminder->tarea->planta->user;
            
            // Enviar correo (o notificación)
            Mail::to($user->email)->send(new TaskReminder($reminder->tarea));
            
            // Marcar como enviado
            $reminder->update(['enviado' => true]);
            
            // Crear nuevo recordatorio para la próxima vez
            if ($reminder->tarea->activa) {
                Recordatorio::create([
                    'tarea_id' => $reminder->tarea_id,
                    'fecha_envio' => $reminder->tarea->proxima_fecha,
                    'enviado' => false
                ]);
            }
        }
        
        $this->info('Reminders sent: ' . $reminders->count());
    }
}