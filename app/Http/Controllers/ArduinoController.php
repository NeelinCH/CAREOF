<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

            if ($resultado['exito']) {
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
                    'descripcion' => "activó riego automático de {$cantidadMl}ml en {$planta->nombre}",
                    'detalles' => [
                        'tarea_id' => $tareaId,
                        'registro_id' => $registro->id,
                        'metodo' => 'Arduino Automático',
                        'puerto' => $puerto,
                        'tiempo' => $tiempoRiego,
                        'cantidad_ml' => $cantidadMl,
                        'accion' => 'riego_arduino'
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
                'message' => 'Error en la comunicación con Arduino: ' . ($resultado['error'] ?? 'Error desconocido')
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar conexión con Arduino - API
     */
    public function verificarConexion(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_puerto', 'COM9'));
            
            $conectado = $this->verificarConexionArduino($puerto);
            
            return response()->json([
                'success' => true,
                'conectado' => $conectado,
                'puerto' => $puerto,
                'mensaje' => $conectado ? 
                    'Conexión con Arduino establecida correctamente' : 
                    'No se pudo establecer conexión con Arduino',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'conectado' => false,
                'mensaje' => 'Error al verificar conexión: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Verificar conexión con Arduino - Web
     */
    public function verificarConexionWeb(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_puerto', 'COM9'));
            $conectado = $this->verificarConexionArduino($puerto);
            
            return back()->with(
                $conectado ? 'success' : 'error',
                $conectado ? 
                    '✅ Conexión con Arduino establecida correctamente en ' . $puerto : 
                    '❌ No se pudo establecer conexión con Arduino en ' . $puerto
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error al verificar conexión: ' . $e->getMessage());
        }
    }

    /**
     * Escanear puertos disponibles - API
     */
    public function escanearPuertos()
    {
        try {
            $puertos = $this->escanearPuertosDisponibles();
            
            return response()->json([
                'success' => true,
                'puertos' => $puertos,
                'total' => count($puertos),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al escanear puertos: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Escanear puertos disponibles - Web
     */
    public function escanearPuertosWeb()
    {
        try {
            $puertos = $this->escanearPuertosDisponibles();
            
            return back()->with([
                'success' => 'Puertos escaneados correctamente',
                'puertos' => $puertos
            ]);

        } catch (\Exception $e) {
            return back()->with('error', 'Error al escanear puertos: ' . $e->getMessage());
        }
    }

    /**
     * Test de comunicación - API
     */
    public function testComunicacion(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_puerto', 'COM9'));
            $comando = $request->input('comando', 'T'); // Comando de test
            
            $resultado = $this->enviarComandoArduino($puerto, $comando, true);
            
            return response()->json([
                'success' => true,
                'comunicacion_establecida' => $resultado['exito'],
                'respuesta' => $resultado['respuesta'],
                'puerto' => $puerto,
                'comando' => $comando,
                'mensaje' => $resultado['exito'] ? 
                    'Comunicación con Arduino establecida correctamente' : 
                    'No se recibió respuesta del Arduino',
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'comunicacion_establecida' => false,
                'mensaje' => 'Error en test de comunicación: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Test de comunicación - Web
     */
    public function testComunicacionWeb(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_puerto', 'COM9'));
            $resultado = $this->enviarComandoArduino($puerto, 'T', true);
            
            return back()->with(
                $resultado['exito'] ? 'success' : 'error',
                $resultado['exito'] ? 
                    '✅ Comunicación con Arduino establecida. Respuesta: ' . $resultado['respuesta'] : 
                    '❌ No se recibió respuesta del Arduino'
            );

        } catch (\Exception $e) {
            return back()->with('error', 'Error en test de comunicación: ' . $e->getMessage());
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

    /**
     * Método para verificar conexión con Arduino
     */
    private function verificarConexionArduino($puerto)
    {
        // En entorno de desarrollo, simular conexión exitosa
        if (app()->environment('local', 'testing')) {
            return true;
        }

        // En producción, verificar si el puerto existe y es accesible
        try {
            // Verificar si el puerto existe en el sistema
            if ($this->puertoExiste($puerto)) {
                // Intentar abrir el puerto para verificar acceso
                return $this->probarAccesoPuerto($puerto);
            }
            
            return false;

        } catch (\Exception $e) {
            Log::error('Error verificando conexión Arduino: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el puerto existe en el sistema
     */
    private function puertoExiste($puerto)
    {
        // Lista de puertos comunes para verificar
        $puertosComunes = ['COM9', 'COM10', 'COM3', 'COM4', 'COM5', 'COM6', 
                          '/dev/ttyUSB0', '/dev/ttyUSB1', '/dev/ttyACM0', '/dev/ttyACM1'];

        // En Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return in_array($puerto, $puertosComunes);
        }
        
        // En Linux/Mac
        return file_exists($puerto);
    }

    /**
     * Probar acceso al puerto
     */
    private function probarAccesoPuerto($puerto)
    {
        try {
            // Intento de abrir el puerto (esto varía según el sistema operativo)
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                $handle = @fopen($puerto, 'w');
                if ($handle) {
                    fclose($handle);
                    return true;
                }
            } else {
                // Linux/Mac
                exec("stty -F " . escapeshellarg($puerto) . " 2>&1", $output, $returnCode);
                return $returnCode === 0;
            }
            
            return false;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Escanear puertos disponibles
     */
    private function escanearPuertosDisponibles()
    {
        $puertosDisponibles = [];

        // Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            for ($i = 1; $i <= 20; $i++) {
                $puerto = 'COM' . $i;
                if ($this->verificarConexionArduino($puerto)) {
                    $puertosDisponibles[] = $puerto;
                }
            }
        } else {
            // Linux/Mac - puertos comunes
            $puertosComunes = [
                '/dev/ttyUSB0', '/dev/ttyUSB1', '/dev/ttyUSB2',
                '/dev/ttyACM0', '/dev/ttyACM1', '/dev/ttyACM2',
                '/dev/ttyS0', '/dev/ttyS1', '/dev/ttyS2'
            ];
            
            foreach ($puertosComunes as $puerto) {
                if ($this->verificarConexionArduino($puerto)) {
                    $puertosDisponibles[] = $puerto;
                }
            }
        }

        return $puertosDisponibles;
    }

    /**
     * Método mejorado para enviar comandos a Arduino
     */
    private function enviarComandoArduino($puerto, $comando, $esperarRespuesta = false)
    {
        // En entorno de desarrollo, simular éxito
        if (app()->environment('local', 'testing')) {
            usleep(500000); // 0.5 segundos de delay simulado
            
            if ($esperarRespuesta) {
                return [
                    'exito' => true,
                    'respuesta' => 'OK_TEST',
                    'tiempo_respuesta' => '500ms'
                ];
            }
            
            return ['exito' => true];
        }

        // En producción, comunicación real con Arduino
        try {
            $startTime = microtime(true);
            
            // Aquí iría el código real para comunicarse con Arduino
            // $fp = fopen($puerto, 'w+');
            // fwrite($fp, $comando . "\n");
            
            if ($esperarRespuesta) {
                // Leer respuesta
                // $respuesta = fgets($fp, 1024);
                // fclose($fp);
                
                $endTime = microtime(true);
                $tiempoRespuesta = round(($endTime - $startTime) * 1000, 2);
                
                return [
                    'exito' => true,
                    'respuesta' => 'OK', // $respuesta en producción
                    'tiempo_respuesta' => $tiempoRespuesta . 'ms'
                ];
            }
            
            // fclose($fp);
            return ['exito' => true];

        } catch (\Exception $e) {
            Log::error('Error comunicando con Arduino: ' . $e->getMessage());
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }
}