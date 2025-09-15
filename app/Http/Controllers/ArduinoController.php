<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ArduinoController extends Controller
{
    public function activarRiego(Request $request, $plantaId, $tareaId)
    {
        $planta = Planta::findOrFail($plantaId);
        $tarea = Tarea::findOrFail($tareaId);

        if (!Auth::user()->can('update', $planta) || !Auth::user()->can('update', $tarea)) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        try {
            // Usar nombres de sesión consistentes
            $puerto = $request->input('puerto', session('arduino_port', 'COM3'));
            $tiempoRiego = $request->input('tiempo', 2000);
            $cantidadMl = $request->input('cantidad_ml', 500);

            // Verificar conexión
            if (!$this->verificarConexionArduino($puerto)) {
                Log::warning("Arduino desconectado en puerto: $puerto");
                return response()->json([
                    'success' => false,
                    'message' => 'Arduino no conectado en ' . $puerto,
                    'conectado' => false
                ], 400);
            }

            // Enviar comando
            $resultado = $this->enviarComandoArduino($puerto, 'R' . $tiempoRiego);

            if ($resultado['exito']) {
                // Registrar en base de datos
                $registro = RegistroRiego::create([
                    'tarea_id' => $tareaId,
                    'user_id' => Auth::id(),
                    'fecha_hora' => now(),
                    'cantidad_ml' => $cantidadMl,
                    'metodo' => 'Arduino',
                    'observaciones' => 'Riego automático. Puerto: ' . $puerto
                ]);

                $tarea->update([
                    'ultima_ejecucion' => now(),
                    'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
                ]);

                Actividad::create([
                    'user_id' => Auth::id(),
                    'planta_id' => $plantaId,
                    'tipo' => 'riego',
                    'descripcion' => "Riego automático de {$cantidadMl}ml",
                    'detalles' => [
                        'tarea_id' => $tareaId,
                        'registro_id' => $registro->id,
                        'puerto' => $puerto,
                        'tiempo' => $tiempoRiego,
                        'cantidad_ml' => $cantidadMl
                    ]
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Riego activado correctamente',
                    'duracion' => $tiempoRiego . 'ms',
                    'cantidad' => $cantidadMl . 'ml',
                    'conectado' => true,
                    'registro_id' => $registro->id
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . ($resultado['error'] ?? 'Comando no ejecutado'),
                'conectado' => true,
                'arduino_response' => $resultado['respuesta'] ?? ''
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error en activarRiego: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno: ' . $e->getMessage(),
                'conectado' => false
            ], 500);
        }
    }

    public function verificarConexion(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_port', 'COM3'));
            
            $conectado = $this->verificarConexionArduino($puerto);
            
            return response()->json([
                'success' => true,
                'conectado' => $conectado,
                'puerto' => $puerto,
                'mensaje' => $conectado ? '✅ Arduino conectado' : '❌ Arduino desconectado'
            ]);

        } catch (\Exception $e) {
            Log::error('Error verificando conexión: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'conectado' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function escanearPuertos()
    {
        try {
            $puertos = $this->escanearPuertosDisponibles();
            
            return response()->json([
                'success' => true,
                'puertos' => $puertos,
                'total' => count($puertos),
                'mensaje' => count($puertos) > 0 ? 'Puertos encontrados' : 'No se encontraron puertos'
            ]);

        } catch (\Exception $e) {
            Log::error('Error escaneando puertos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'mensaje' => 'Error: ' . $e->getMessage(),
                'puertos' => []
            ], 500);
        }
    }

    public function testComunicacion(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_port', 'COM3'));
            
            if (!$this->verificarConexionArduino($puerto)) {
                return response()->json([
                    'success' => false,
                    'comunicacion_establecida' => false,
                    'mensaje' => 'Puerto no disponible'
                ]);
            }
            
            $resultado = $this->enviarComandoArduino($puerto, 'TEST');
            
            return response()->json([
                'success' => $resultado['exito'],
                'comunicacion_establecida' => $resultado['exito'],
                'respuesta' => $resultado['respuesta'] ?? '',
                'mensaje' => $resultado['exito'] ? 
                    'Comunicación exitosa' : 
                    'Error de comunicación: ' . ($resultado['error'] ?? 'Sin respuesta')
            ]);

        } catch (\Exception $e) {
            Log::error('Error test comunicación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'comunicacion_establecida' => false,
                'mensaje' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function estadoArduino(Request $request)
    {
        try {
            $puerto = $request->input('puerto', session('arduino_port', 'COM3'));
            
            $estado = [
                'puerto_configurado' => $puerto,
                'conectado' => $this->verificarConexionArduino($puerto),
                'timestamp' => now()->toISOString()
            ];

            if ($estado['conectado']) {
                $resultado = $this->enviarComandoArduino($puerto, 'STATUS');
                $estado['comunicacion_ok'] = $resultado['exito'];
                $estado['respuesta_arduino'] = $resultado['respuesta'] ?? '';
            }

            return response()->json([
                'success' => true,
                'estado' => $estado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function configuracionArduino(Request $request)
    {
        try {
            $validated = $request->validate([
                'puerto' => 'required|string',
                'baud_rate' => 'sometimes|numeric'
            ]);

            // Guardar en sesión con nombres consistentes
            session([
                'arduino_port' => $validated['puerto'],
                'arduino_baud_rate' => $validated['baud_rate'] ?? 9600
            ]);

            return response()->json([
                'success' => true,
                'mensaje' => 'Configuración guardada',
                'configuracion' => [
                    'puerto' => $validated['puerto'],
                    'baud_rate' => $validated['baud_rate'] ?? 9600
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    private function verificarConexionArduino($puerto)
    {
        try {
            // En Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Método más confiable para Windows
                $command = "powershell \"Get-CimInstance -Class Win32_SerialPort | Select-Object DeviceID | ForEach-Object { $_.DeviceID }\"";
                $output = [];
                exec($command, $output, $returnCode);
                
                return in_array($puerto, $output);
            } 
            // En Linux/Mac
            else {
                return file_exists($puerto) && is_readable($puerto) && is_writable($puerto);
            }
        } catch (\Exception $e) {
            Log::error('Error verificando conexión Arduino: ' . $e->getMessage());
            return false;
        }
    }

    private function escanearPuertosDisponibles()
    {
        $puertos = [];

        try {
            // Para Windows
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                $command = "powershell \"Get-CimInstance -Class Win32_SerialPort | Select-Object DeviceID, Description | ForEach-Object { $_.DeviceID + '|' + $_.Description }\"";
                $output = [];
                exec($command, $output);
                
                foreach ($output as $line) {
                    if (strpos($line, '|') !== false) {
                        list($puerto, $descripcion) = explode('|', $line, 2);
                        $puertos[] = [
                            'puerto' => trim($puerto),
                            'descripcion' => trim($descripcion)
                        ];
                    }
                }
                
                // Fallback para puertos COM comunes
                if (empty($puertos)) {
                    for ($i = 1; $i <= 20; $i++) {
                        $puerto = 'COM' . $i;
                        if ($this->verificarConexionArduino($puerto)) {
                            $puertos[] = [
                                'puerto' => $puerto,
                                'descripcion' => 'Puerto serie detectado'
                            ];
                        }
                    }
                }
            } 
            // Para Linux/Mac
            else {
                $puertosComunes = [
                    '/dev/ttyUSB0', '/dev/ttyUSB1', '/dev/ttyUSB2', '/dev/ttyUSB3',
                    '/dev/ttyACM0', '/dev/ttyACM1', '/dev/ttyACM2', '/dev/ttyACM3',
                    '/dev/ttyS0', '/dev/ttyS1', '/dev/ttyS2', '/dev/ttyS3'
                ];
                
                foreach ($puertosComunes as $puerto) {
                    if (file_exists($puerto)) {
                        $puertos[] = [
                            'puerto' => $puerto,
                            'descripcion' => 'Puerto serie Unix'
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error escaneando puertos: ' . $e->getMessage());
        }

        return $puertos;
    }

    private function enviarComandoArduino($puerto, $comando)
    {
        try {
            $baudRate = session('arduino_baud_rate', 9600);
            
            // En Windows usar mode para configurar el puerto
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                exec("mode $puerto BAUD=$baudRate PARITY=N DATA=8 STOP=1");
            }

            // Abrir conexión
            $fp = @fopen($puerto, 'w+');
            
            if (!$fp) {
                return ['exito' => false, 'error' => 'No se pudo abrir el puerto ' . $puerto];
            }
            
            // Configurar timeout
            stream_set_timeout($fp, 3);
            stream_set_blocking($fp, false);
            
            // Limpiar buffer
            while (fgets($fp) !== false) {
                // Vaciar buffer de entrada
            }
            
            // Enviar comando
            fwrite($fp, $comando . "\r\n");
            fflush($fp);
            
            // Dar tiempo al Arduino para procesar
            usleep(500000); // 500ms
            
            // Leer respuesta
            $respuesta = '';
            $timeout = time() + 5;
            
            while (time() < $timeout) {
                $linea = fgets($fp);
                if ($linea !== false) {
                    $respuesta .= $linea;
                    
                    // Buscar respuestas conocidas
                    if (strpos($respuesta, 'OK') !== false || 
                        strpos($respuesta, 'DONE') !== false ||
                        strpos($respuesta, 'ERROR') !== false) {
                        break;
                    }
                }
                usleep(100000); // 100ms
            }
            
            fclose($fp);
            
            $respuestaLimpia = trim($respuesta);
            $exito = !empty($respuestaLimpia) && 
                     (strpos($respuestaLimpia, 'OK') !== false || 
                      strpos($respuestaLimpia, 'DONE') !== false);
            
            return [
                'exito' => $exito,
                'respuesta' => $respuestaLimpia,
                'comando_enviado' => $comando
            ];

        } catch (\Exception $e) {
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }
}