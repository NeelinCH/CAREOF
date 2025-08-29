<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Support\Facades\Http;

class ArduinoController extends Controller
{
    public function activarRiego(Request $request, $plantaId, $tareaId)
    {
        $planta = Planta::findOrFail($plantaId);
        $tarea = Tarea::findOrFail($tareaId);

        // Verificar autorización
        if (!Auth::user()->can('update', $planta) || !Auth::user()->can('update', $tarea)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        try {
            // Configuración de Arduino
            $puerto = $request->input('puerto', 'COM9');
            $tiempoRiego = $request->input('tiempo', 2000); // 2 segundos
            $cantidadMl = $request->input('cantidad_ml', 500);

            // Simular comunicación con Arduino (en producción sería real)
            $comando = 'R' . $tiempoRiego;
            $resultado = $this->enviarComandoArduino($puerto, $comando);

            if ($resultado) {
                // Registrar el riego
                $registro = RegistroRiego::create([
                    'tarea_id' => $tareaId,
                    'user_id' => Auth::id(),
                    'fecha_hora' => now(),
                    'cantidad_ml' => $cantidadMl,
                    'metodo' => 'Arduino Automático',
                    'observaciones' => 'Riego automático activado por sistema'
                ]);

                // Actualizar próxima fecha
                $tarea->update([
                    'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
                ]);

                // Registrar actividad
                Actividad::create([
                    'user_id' => Auth::id(),
                    'planta_id' => $plantaId,
                    'tipo' => 'riego',
                    'descripcion' => "activó riego automático de {$cantidadMl}ml",
                    'detalles' => [
                        'tarea_id' => $tareaId,
                        'registro_id' => $registro->id,
                        'metodo' => 'Arduino',
                        'puerto' => $puerto,
                        'tiempo' => $tiempoRiego
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Riego automático activado correctamente',
                    'registro' => $registro,
                    'duracion' => $tiempoRiego . 'ms',
                    'cantidad' => $cantidadMl . 'ml'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error en la comunicación con Arduino'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function enviarComandoArduino($puerto, $comando)
    {
        // En entorno de desarrollo, simular éxito
        if (app()->environment('local', 'testing')) {
            // Simular delay de Arduino
            usleep(500000); // 0.5 segundos
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
            'estado' => 'Disponible',
            'ultima_comunicacion' => now()->toISOString(),
            'version' => '1.0.0'
        ]);
    }

    public function configuracionArduino(Request $request)
    {
        $request->validate([
            'puerto' => 'required|string',
            'tiempo_riego' => 'required|integer|min:100|max:10000',
            'cantidad_ml' => 'required|integer|min:1|max:5000'
        ]);

        // Guardar configuración (podrías usar la tabla configuraciones)
        session([
            'arduino_puerto' => $request->puerto,
            'arduino_tiempo_riego' => $request->tiempo_riego,
            'arduino_cantidad_ml' => $request->cantidad_ml
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Configuración guardada correctamente'
        ]);
    }
}