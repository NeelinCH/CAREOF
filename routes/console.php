<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Comando personalizado para probar recordatorios
Artisan::command('test-reminders', function () {
    $this->call('reminders:test');
})->describe('Test the reminders system (alias for reminders:test)');

// Comando para ver el estado de los recordatorios
Artisan::command('reminders:status', function () {
    $pendientes = \App\Models\Recordatorio::where('enviado', false)->count();
    $enviados = \App\Models\Recordatorio::where('enviado', true)->count();
    $conError = \App\Models\Recordatorio::where('error_mensaje', '!=', null)->count();
    
    $this->info("Estado de recordatorios:");
    $this->line("📋 Pendientes: {$pendientes}");
    $this->line("✅ Enviados: {$enviados}");
    $this->line("❌ Con error: {$conError}");
})->describe('Show the status of reminders');