<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArduinoConfigController extends Controller
{
    /**
     * Muestra el formulario de configuración
     */
    public function index()
    {
        // Recuperar configuración actual si existe
        $currentPort = old('port', session('arduino_port', ''));
        $currentBaudRate = old('baud_rate', session('arduino_baud_rate', '9600'));
        
        return view('arduino-config', compact('currentPort', 'currentBaudRate'));
    }

    /**
     * Guarda la configuración del Arduino
     */
    public function saveConfig(Request $request)
    {
        $validated = $request->validate([
            'port' => 'required|string',
            'baud_rate' => 'required|numeric',
        ]);
        
        // Guardar en sesión
        session([
            'arduino_port' => $validated['port'],
            'arduino_baud_rate' => $validated['baud_rate']
        ]);
        
        // Redirigir a la página anterior o a una por defecto
        $previousUrl = $request->input('previous_url', route('plantas.index'));
        
        return redirect($previousUrl)
            ->with('success', 'Configuración de Arduino guardada correctamente');
    }

    /**
     * Prueba la conexión con el Arduino (para AJAX)
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'port' => 'required|string',
            'baud_rate' => 'required|numeric',
        ]);
        
        $port = $request->input('port');
        $baudRate = $request->input('baud_rate');
        
        // Simular prueba de conexión
        $success = $this->simulateConnectionTest($port, $baudRate);
        
        return response()->json([
            'success' => $success,
            'message' => $success 
                ? "Conexión exitosa con Arduino en $port ($baudRate baudios)"
                : "No se pudo conectar con Arduino en $port"
        ]);
    }
    
    /**
     * Simula la prueba de conexión con Arduino
     */
    private function simulateConnectionTest($port, $baudRate)
    {
        // Simular fallo para puertos que no sean COM9 o COM10
        if (!in_array($port, ['COM9', 'COM10'])) {
            return false;
        }
        
        // Simular una probabilidad de éxito del 80%
        return rand(1, 100) <= 80;
    }
}