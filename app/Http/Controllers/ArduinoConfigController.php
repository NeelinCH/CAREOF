<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ArduinoConfigController extends Controller
{
    public function index()
    {
        // Nombres consistentes de sesión
        $currentPort = old('port', session('arduino_port', 'COM3'));
        $currentBaudRate = old('baud_rate', session('arduino_baud_rate', '9600'));
        
        return view('configuracion-arduino', compact('currentPort', 'currentBaudRate'));
    }

    public function saveConfig(Request $request)
    {
        $validated = $request->validate([
            'port' => 'required|string|max:20',
            'baud_rate' => 'required|integer|in:9600,19200,38400,57600,115200',
        ], [
            'port.required' => 'El puerto es obligatorio',
            'port.max' => 'El puerto no debe exceder 20 caracteres',
            'baud_rate.required' => 'La velocidad de baudios es obligatoria',
            'baud_rate.in' => 'La velocidad de baudios debe ser válida (9600, 19200, 38400, 57600, 115200)'
        ]);
        
        try {
            // Usar nombres de sesión consistentes
            session([
                'arduino_port' => $validated['port'],
                'arduino_baud_rate' => $validated['baud_rate']
            ]);
            
            Log::info('Configuración Arduino actualizada', [
                'puerto' => $validated['port'],
                'baud_rate' => $validated['baud_rate'],
                'user_id' => auth()->id()
            ]);
            
            return redirect($request->input('previous_url', route('plantas.index')))
                ->with('success', 'Configuración de Arduino guardada correctamente');
                
        } catch (\Exception $e) {
            Log::error('Error guardando configuración Arduino: ' . $e->getMessage());
            
            return back()
                ->withInput()
                ->with('error', 'Error al guardar la configuración: ' . $e->getMessage());
        }
    }

    public function testConnection(Request $request)
    {
        $validated = $request->validate([
            'port' => 'required|string',
            'baud_rate' => 'sometimes|integer'
        ]);

        try {
            $puerto = $validated['port'];
            $baudRate = $validated['baud_rate'] ?? 9600;

            // Verificar si el puerto existe
            $conectado = $this->verificarPuerto($puerto);

            if ($conectado) {
                return response()->json([
                    'success' => true,
                    'message' => "Conexión exitosa al puerto $puerto",
                    'port' => $puerto,
                    'baud_rate' => $baudRate
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "No se pudo conectar al puerto $puerto",
                    'port' => $puerto
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAvailablePorts()
    {
        try {
            $puertos = $this->escanearPuertos();
            
            return response()->json([
                'success' => true,
                'ports' => $puertos,
                'count' => count($puertos)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'ports' => []
            ], 500);
        }
    }

    private function verificarPuerto($puerto)
    {
        try {
            // Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "powershell \"Get-CimInstance -Class Win32_SerialPort | Where-Object { $_.DeviceID -eq '$puerto' }\"";
                $output = [];
                exec($command, $output, $returnCode);
                return !empty($output);
            } 
            // Linux/Mac
            else {
                return file_exists($puerto) && is_readable($puerto);
            }
        } catch (\Exception $e) {
            Log::error('Error verificando puerto: ' . $e->getMessage());
            return false;
        }
    }

    private function escanearPuertos()
    {
        $puertos = [];

        try {
            // Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "powershell \"Get-CimInstance -Class Win32_SerialPort | Select-Object DeviceID\"";
                $output = [];
                exec($command, $output);
                
                foreach ($output as $line) {
                    if (preg_match('/COM\d+/', $line, $matches)) {
                        $puertos[] = $matches[0];
                    }
                }
            } 
            // Linux/Mac
            else {
                $posiblesPuertos = ['/dev/ttyUSB*', '/dev/ttyACM*', '/dev/ttyS*'];
                
                foreach ($posiblesPuertos as $patron) {
                    $encontrados = glob($patron);
                    $puertos = array_merge($puertos, $encontrados);
                }
            }
        } catch (\Exception $e) {
            Log::error('Error escaneando puertos: ' . $e->getMessage());
        }

        return array_unique($puertos);
    }
}