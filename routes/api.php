<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArduinoController;
use App\Http\Controllers\ArduinoConfigController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Rutas principales de Arduino
    Route::prefix('arduino')->name('api.arduino.')->group(function () {
        // Control de riego
        Route::post('/activar-riego/{planta}/{tarea}', [ArduinoController::class, 'activarRiego'])
            ->name('activar-riego');
        
        // Verificación y estado
        Route::get('/verificar-conexion', [ArduinoController::class, 'verificarConexion'])
            ->name('verificar-conexion');
        Route::get('/estado', [ArduinoController::class, 'estadoArduino'])
            ->name('estado');
        
        // Configuración
        Route::post('/configuracion', [ArduinoController::class, 'configuracionArduino'])
            ->name('configuracion');
        
        // Detección de puertos
        Route::get('/escanear-puertos', [ArduinoController::class, 'escanearPuertos'])
            ->name('escanear-puertos');
        Route::post('/test-comunicacion', [ArduinoController::class, 'testComunicacion'])
            ->name('test-comunicacion');
    });

    // Rutas de configuración web
    Route::prefix('config')->name('api.config.')->group(function () {
        Route::post('/arduino/test-connection', [ArduinoConfigController::class, 'testConnection'])
            ->name('arduino.test-connection');
        Route::get('/arduino/available-ports', [ArduinoConfigController::class, 'getAvailablePorts'])
            ->name('arduino.available-ports');
    });
});

// Rutas públicas para Arduino (si es necesario)
Route::get('/arduino/puertos-disponibles', [ArduinoController::class, 'escanearPuertos'])
    ->name('api.arduino.puertos-publico');

// Ruta fallback para manejar rutas API no definidas
Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint API no encontrado. Verifica la URL.',
        'status' => 404
    ], 404);
});