<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Planta;
use App\Models\Tarea;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RiegoControl extends Component
{
    public $plantaId;
    public $tareaId;
    public $planta;
    public $tarea;
    
    // Estados del componente
    public $conectado = false;
    public $regando = false;
    public $verificandoConexion = false;
    public $escaneandoPuertos = false;
    public $estadoArduino = 'Desconocido';
    
    // Configuración
    public $puertoSeleccionado;
    public $cantidadMl = 500;
    public $tiempoRiego = 2000;
    public $puertosDisponibles = [];
    
    // Mensajes
    public $mensaje = '';
    public $tipoMensaje = 'info'; // success, error, warning, info
    public $ultimaAccion = '';

    protected $rules = [
        'cantidadMl' => 'required|integer|min:50|max:5000',
        'tiempoRiego' => 'required|integer|min:1000|max:30000',
        'puertoSeleccionado' => 'required|string'
    ];

    protected $messages = [
        'cantidadMl.required' => 'La cantidad es obligatoria',
        'cantidadMl.min' => 'Mínimo 50ml',
        'cantidadMl.max' => 'Máximo 5000ml',
        'tiempoRiego.required' => 'El tiempo es obligatorio',
        'tiempoRiego.min' => 'Mínimo 1 segundo (1000ms)',
        'tiempoRiego.max' => 'Máximo 30 segundos (30000ms)',
        'puertoSeleccionado.required' => 'Selecciona un puerto'
    ];

    public function mount($plantaId, $tareaId)
    {
        $this->plantaId = $plantaId;
        $this->tareaId = $tareaId;
        $this->planta = Planta::findOrFail($plantaId);
        $this->tarea = Tarea::findOrFail($tareaId);
        
        // Cargar configuración de sesión con más opciones de puertos
        $this->puertoSeleccionado = session('arduino_port', 'COM3');
        $this->puertosDisponibles = $this->obtenerPuertosPorDefecto();
        
        // Verificar conexión inicial
        $this->verificarConexionInicial();
    }
    
    /**
     * Obtener lista de puertos por defecto incluyendo COM9 y COM10
     */
    private function obtenerPuertosPorDefecto()
    {
        return [
            ['puerto' => 'COM3', 'descripcion' => 'Puerto COM3 (Común)'],
            ['puerto' => 'COM4', 'descripcion' => 'Puerto COM4 (Común)'],
            ['puerto' => 'COM9', 'descripcion' => 'Puerto COM9 (Arduino Uno)'],
            ['puerto' => 'COM10', 'descripcion' => 'Puerto COM10 (Alternativo)'],
            ['puerto' => 'COM5', 'descripcion' => 'Puerto COM5'],
            ['puerto' => 'COM6', 'descripcion' => 'Puerto COM6'],
        ];
    }

    public function render()
    {
        return view('livewire.riego-control');
    }

    public function verificarConexionInicial()
    {
        try {
            $this->verificandoConexion = true;
            $this->verificarConexion();
        } catch (\Exception $e) {
            $this->mostrarMensaje('Error al verificar conexión inicial', 'error');
        } finally {
            $this->verificandoConexion = false;
        }
    }

    public function verificarConexion()
    {
        try {
            $this->verificandoConexion = true;
            $this->limpiarMensaje();

            // Usar URL absoluta en lugar de route() para evitar problemas
            $url = url('/api/arduino/verificar-conexion') . '?puerto=' . urlencode($this->puertoSeleccionado);
            
            $response = Http::timeout(20)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->conectado = $data['conectado'] ?? false;
                $this->estadoArduino = $this->conectado ? 'Disponible' : 'No disponible';
                $this->mostrarMensaje($data['mensaje'] ?? '', $this->conectado ? 'success' : 'warning');
                $this->ultimaAccion = 'Verificación de conexión - ' . now()->format('H:i:s');
            } else {
                $this->conectado = false;
                $this->estadoArduino = 'Error';
                $this->mostrarMensaje('Error al verificar conexión', 'error');
            }

        } catch (\Exception $e) {
            $this->conectado = false;
            $this->estadoArduino = 'Error';
            $this->mostrarMensaje('Error de comunicación: ' . $e->getMessage(), 'error');
            Log::error('Error verificando conexión Arduino: ' . $e->getMessage());
        } finally {
            $this->verificandoConexion = false;
        }
    }

    public function escanearPuertos()
    {
        try {
            $this->escaneandoPuertos = true;
            $this->limpiarMensaje();

            // Usar URL absoluta para la nueva ruta de puertos extendidos
            $url = url('/api/arduino/puertos-extendidos');
            
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $this->puertosDisponibles = $data['puertos'] ?? $this->obtenerPuertosPorDefecto();
                
                $mensaje = $data['total'] > 0 ? 
                    "Encontrados {$data['total']} puertos" : 
                    'No se encontraron puertos disponibles, usando lista predeterminada';
                    
                $this->mostrarMensaje($mensaje, $data['total'] > 0 ? 'success' : 'warning');
                
                // Auto-seleccionar el primer puerto si no hay uno seleccionado
                if (!empty($this->puertosDisponibles) && !$this->puertoSeleccionado) {
                    $this->puertoSeleccionado = is_array($this->puertosDisponibles[0]) ? 
                        $this->puertosDisponibles[0]['puerto'] : 
                        $this->puertosDisponibles[0];
                }
            } else {
                // Fallback a puertos por defecto si la API falla
                $this->puertosDisponibles = $this->obtenerPuertosPorDefecto();
                $this->mostrarMensaje('Usando lista predeterminada de puertos', 'warning');
            }

        } catch (\Exception $e) {
            // Fallback a puertos por defecto en caso de error
            $this->puertosDisponibles = $this->obtenerPuertosPorDefecto();
            $this->mostrarMensaje('Error al escanear puertos, usando lista predeterminada', 'error');
            Log::error('Error escaneando puertos: ' . $e->getMessage());
        } finally {
            $this->escaneandoPuertos = false;
        }
    }

    public function testComunicacion()
    {
        $this->validate(['puertoSeleccionado' => 'required']);

        try {
            $this->limpiarMensaje();

            // Usar URL absoluta
            $url = url('/api/arduino/test-comunicacion');
            
            $response = Http::timeout(10)->post($url, [
                'puerto' => $this->puertoSeleccionado
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->conectado = $data['comunicacion_establecida'] ?? false;
                $this->estadoArduino = $this->conectado ? 'Comunicación OK' : 'Sin comunicación';
                $this->mostrarMensaje($data['mensaje'] ?? '', $this->conectado ? 'success' : 'error');
                $this->ultimaAccion = 'Test de comunicación - ' . now()->format('H:i:s');
            } else {
                $this->estadoArduino = 'Error de test';
                $this->mostrarMensaje('Error en el test de comunicación', 'error');
            }

        } catch (\Exception $e) {
            $this->estadoArduino = 'Error';
            $this->mostrarMensaje('Error: ' . $e->getMessage(), 'error');
            Log::error('Error test comunicación: ' . $e->getMessage());
        }
    }

    public function activarRiego()
    {
        $this->validate();

        if ($this->regando) {
            return;
        }

        try {
            $this->regando = true;
            $this->limpiarMensaje();
            $this->estadoArduino = 'Regando...';

            // Guardar configuración en sesión
            session(['arduino_port' => $this->puertoSeleccionado]);

            // Usar URL absoluta
            $url = url("/api/arduino/activar-riego/{$this->plantaId}/{$this->tareaId}");
            
            $response = Http::timeout(30)->post($url, [
                'puerto' => $this->puertoSeleccionado,
                'cantidad_ml' => $this->cantidadMl,
                'tiempo' => $this->tiempoRiego
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    $this->estadoArduino = 'Riego completado';
                    $this->mostrarMensaje(
                        "¡Riego completado! {$data['cantidad']} durante {$data['duracion']}", 
                        'success'
                    );
                    $this->ultimaAccion = "Riego: {$data['cantidad']} - " . now()->format('H:i:s');
                    
                    // Refrescar la página después de 3 segundos para mostrar el nuevo registro
                    $this->emit('riegoCompletado');
                    $this->dispatchBrowserEvent('riego-completado', [
                        'message' => $data['message']
                    ]);
                    
                } else {
                    $this->estadoArduino = 'Error en riego';
                    $this->mostrarMensaje($data['message'], 'error');
                    $this->conectado = $data['conectado'] ?? false;
                }
                
            } else {
                $this->estadoArduino = 'Error';
                $this->mostrarMensaje('Error al activar el riego', 'error');
            }

        } catch (\Exception $e) {
            $this->estadoArduino = 'Error';
            $this->mostrarMensaje('Error: ' . $e->getMessage(), 'error');
            Log::error('Error activando riego: ' . $e->getMessage());
        } finally {
            $this->regando = false;
        }
    }

    public function updatedPuertoSeleccionado()
    {
        if ($this->puertoSeleccionado) {
            session(['arduino_port' => $this->puertoSeleccionado]);
            $this->verificarConexion();
        }
    }

    private function mostrarMensaje($mensaje, $tipo = 'info')
    {
        $this->mensaje = $mensaje;
        $this->tipoMensaje = $tipo;
    }

    private function limpiarMensaje()
    {
        $this->mensaje = '';
        $this->tipoMensaje = 'info';
    }

    public function refreshData()
    {
        $this->verificarConexion();
    }

    public function resetForm()
    {
        $this->cantidadMl = 500;
        $this->tiempoRiego = 2000;
        $this->limpiarMensaje();
        $this->ultimaAccion = '';
        $this->estadoArduino = 'Desconocido';
    }

    public function cerrarMensaje()
    {
        $this->limpiarMensaje();
    }
}