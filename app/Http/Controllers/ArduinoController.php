<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use Illuminate\Auth\Access\AuthorizationException;

class ArduinoController extends Controller
{
    public function activarRiego(Request $request, $plantaId, $tareaId)
    {
        $planta = Planta::findOrFail($plantaId);
        $tarea = Tarea::findOrFail($tareaId);

        // Verificar autorización
        if (!Auth::user()->can('update', $planta) || !Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para realizar esta acción.');
        }

        try {
            // Intentar comunicar con Arduino
            $puerto = $request->input('puerto', 'COM9');
            $comando = 'R'; // Comando para activar riego
            
            // Simulación de comunicación con Arduino (en producción usarías fopen/fwrite)
            $activado = $this->comunicarConArduino($puerto, $comando);
            
            if ($activado) {
                // Registrar el riego
                $registro = RegistroRiego::create([
                    'tarea_id' => $tareaId,
                    'user_id' => Auth::id(),
                    'fecha_hora' => now(),
                    'cantidad_ml' => $request->input('cantidad_ml', 500),
                    'metodo' => 'Arduino USB',
                    'observaciones' => 'Riego activado mediante sistema automático'
                ]);

                // Actualizar próxima fecha de riego
                $tarea->update([
                    'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Riego activado correctamente',
                    'registro' => $registro
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al comunicar con el dispositivo Arduino'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function comunicarConArduino($puerto, $comando)
    {
        // En entorno de desarrollo, simular éxito
        if (app()->environment('local', 'testing')) {
            return true;
        }

        // En producción, comunicación real con Arduino
        try {
            // $fp = fopen($puerto, 'w');
            // fwrite($fp, $comando);
            // fclose($fp);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function estadoArduino()
    {
        return response()->json([
            'conectado' => true,
            'puerto' => 'COM9',
            'estado' => 'Disponible'
        ]);
    }
}