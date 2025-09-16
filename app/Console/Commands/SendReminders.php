<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Recordatorio;
use Illuminate\Support\Facades\Mail;
use App\Mail\TaskReminder;
use Illuminate\Support\Facades\Log;

class SendReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Send task reminders to users';
    
    protected $maxAttempts = 3;

    public function handle()
    {
        $now = now();
        
        $reminders = Recordatorio::with(['tarea.planta.user', 'tarea.planta'])
            ->where('fecha_envio', '<=', $now)
            ->where('enviado', false)
            ->where('intentos', '<', $this->maxAttempts)
            ->get();
        
        $sentCount = 0;
        $errorCount = 0;
        
        foreach ($reminders as $reminder) {
            try {
                $user = $reminder->tarea->planta->user;
                
                if (!$user || !$user->email) {
                    throw new \Exception('Usuario o email no encontrado');
                }
                
                // Enviar correo desde rosinethesis2018@gmail.com
                Mail::to($user->email)->send(new TaskReminder($reminder->tarea));
                
                // Marcar como enviado
                $reminder->update([
                    'enviado' => true,
                    'enviado_at' => now(),
                    'intentos' => $reminder->intentos + 1
                ]);
                
                $sentCount++;
                
                // Crear nuevo recordatorio
                if ($reminder->tarea->activa && $reminder->tarea->proxima_fecha) {
                    Recordatorio::create([
                        'tarea_id' => $reminder->tarea_id,
                        'fecha_envio' => $reminder->tarea->proxima_fecha,
                        'enviado' => false,
                        'tipo_recordatorio' => 'email'
                    ]);
                }
                
            } catch (\Exception $e) {
                $reminder->update([
                    'error_mensaje' => $e->getMessage(),
                    'intentos' => $reminder->intentos + 1
                ]);
                
                $errorCount++;
                Log::error('Error enviando recordatorio: ' . $e->getMessage(), [
                    'recordatorio_id' => $reminder->id,
                    'tarea_id' => $reminder->tarea_id
                ]);
            }
        }
        
        $this->info("Reminders sent: {$sentCount}, Errors: {$errorCount}");
        
        // Log adicional para debugging
        if ($sentCount > 0 || $errorCount > 0) {
            Log::info("Comando reminders:send ejecutado - Enviados: {$sentCount}, Errores: {$errorCount}");
        }
    }
}