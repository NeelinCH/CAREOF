<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArduinoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/arduino/activar-riego/{planta}/{tarea}', [ArduinoController::class, 'activarRiego']);
    Route::get('/arduino/estado', [ArduinoController::class, 'estadoArduino']);
    Route::post('/arduino/configuracion', [ArduinoController::class, 'configuracionArduino']);
    
    // Nuevas rutas para verificación de conexión
    Route::get('/arduino/verificar-conexion', [ArduinoController::class, 'verificarConexion'])
        ->name('api.arduino.verificar-conexion');
    Route::get('/arduino/escanear-puertos', [ArduinoController::class, 'escanearPuertos'])
        ->name('api.arduino.escanear-puertos');
    Route::post('/arduino/test-comunicacion', [ArduinoController::class, 'testComunicacion'])
        ->name('api.arduino.test-comunicacion');
});