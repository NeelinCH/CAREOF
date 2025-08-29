<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArduinoController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/arduino/activar-riego/{planta}/{tarea}', [ArduinoController::class, 'activarRiego']);
    Route::get('/arduino/estado', [ArduinoController::class, 'estadoArduino']);
    Route::post('/arduino/configuracion', [ArduinoController::class, 'configuracionArduino']);
});