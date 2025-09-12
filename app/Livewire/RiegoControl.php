<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Tarea;
use App\Models\Planta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RiegoControl extends Component
{
    public $planta;
    public $tarea;
    public $puerto = 'COM9';
    public $tiempoRiego = 2000;
    public $cantidadMl = 500;
    public $mensaje = '';
    public $mostrarConfiguracion = false;
    public $estadoArduino = 'Desconectado';
    public $ultimaAccion = null;
    
    // Variables temporales para la configuración
    public $puertoTemp;
    public $tiempoRiegoTemp;
    public $cantidadMlTemp;

    // Propiedades para recibir los IDs
    public $plantaId;
    public $tareaId;

    protected $listeners = [
        'riegoActivado' => 'actualizarEstado',
        'mostrarConfiguracionUpdated' => 'manejarActualizacionModal'
    ];

    public function mount()
    {
        // Obtener los IDs de las propiedades y cargar los modelos
        $this->planta = Planta::findOrFail($this->plantaId);
        $this->tarea = Tarea::findOrFail($this->tareaId);
        $this->verificarArduino();
        
        // Cargar configuración guardada en sesión
        $this->cargarConfiguracion();
    }

    public function cargarConfiguracion()
    {
        $this->puerto = Session::get('arduino_puerto', 'COM9');
        $this->tiempoRiego = Session::get('arduino_tiempo_riego', 2000);
        $this->cantidadMl = Session::get('arduino_cantidad_ml', 500);
    }

    public function abrirConfiguracion()
    {
        // Guardar valores actuales en variables temporales
        $this->puertoTemp = $this->puerto;
        $this->tiempoRiegoTemp = $this->tiempoRiego;
        $this->cantidadMlTemp = $this->cantidadMl;
        
        $this->mostrarConfiguracion = true;
        $this->dispatch('mostrarConfiguracionUpdated', value: true);
    }

    public function guardarConfiguracion()
    {
        $this->validate([
            'puertoTemp' => 'required|string',
            'tiempoRiegoTemp' => 'required|integer|min:100|max:10000',
            'cantidadMlTemp' => 'required|integer|min:1|max:5000'
        ]);

        // Guardar en variables principales
        $this->puerto = $this->puertoTemp;
        $this->tiempoRiego = $this->tiempoRiegoTemp;
        $this->cantidadMl = $this->cantidadMlTemp;

        // Guardar en sesión
        Session::put('arduino_puerto', $this->puerto);
        Session::put('arduino_tiempo_riego', $this->tiempoRiego);
        Session::put('arduino_cantidad_ml', $this->cantidadMl);

        $this->mensaje = '✅ Configuración guardada correctamente';
        $this->mostrarConfiguracion = false;
        $this->dispatch('mostrarConfiguracionUpdated', value: false);
        
        // Emitir evento para actualizar la interfaz
        $this->dispatch('configuracionGuardada');
    }

    public function cancelarConfiguracion()
    {
        // Restaurar valores originales sin guardar
        $this->puertoTemp = $this->puerto;
        $this->tiempoRiegoTemp = $this->tiempoRiego;
        $this->cantidadMlTemp = $this->cantidadMl;
        
        $this->mostrarConfiguracion = false;
        $this->mensaje = '❌ Configuración cancelada';
        $this->dispatch('mostrarConfiguracionUpdated', value: false);
        
        // Limpiar mensaje después de 2 segundos
        $this->dispatchBrowserEvent('limpiarMensaje');
    }

    public function verificarArduino()
    {
        try {
            $response = Http::timeout(3)->get(url('/api/arduino/estado'));
            $this->estadoArduino = $response->json()['estado'] ?? 'Error';
        } catch (\Exception $e) {
            $this->estadoArduino = 'Desconectado';
        }
    }

    public function verificarConexion()
    {
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->get(url('/api/arduino/verificar-conexion'), [
                    'puerto' => $this->puerto
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->estadoArduino = $data['conectado'] ? 'Disponible' : 'Desconectado';
                
                $this->mensaje = $data['conectado'] ? 
                    '✅ ' . $data['mensaje'] : 
                    '❌ ' . $data['mensaje'];
                    
                $this->dispatchBrowserEvent('notificacion', [
                    'tipo' => $data['conectado'] ? 'success' : 'error',
                    'mensaje' => $data['mensaje']
                ]);
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error al verificar conexión';
            $this->estadoArduino = 'Error';
        }
    }

    public function testComunicacion()
    {
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->post(url('/api/arduino/test-comunicacion'), [
                    'puerto' => $this->puerto,
                    'comando' => 'T'
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $this->mensaje = $data['comunicacion_establecida'] ? 
                    '✅ ' . $data['mensaje'] . ' Respuesta: ' . $data['respuesta'] : 
                    '❌ ' . $data['mensaje'];
                    
                $this->dispatchBrowserEvent('notificacion', [
                    'tipo' => $data['comunicacion_establecida'] ? 'success' : 'error',
                    'mensaje' => $data['mensaje']
                ]);
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error en test de comunicación';
        }
    }

    public function escanearPuertos()
    {
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->get(url('/api/arduino/escanear-puertos'));

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['puertos'])) {
                    $this->mensaje = '✅ Found ' . $data['total'] . ' ports available';
                    $this->dispatchBrowserEvent('puertosEscaneados', [
                        'puertos' => $data['puertos']
                    ]);
                } else {
                    $this->mensaje = '❌ No ports found';
                }
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error scanning ports';
        }
    }

    public function activarRiego()
    {
        // Prevenir activación si el modal está abierto
        if ($this->mostrarConfiguracion) {
            return;
        }

        try {
            // USAR LA RUTA CORRECTA SEGÚN TU API
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->post(url("/api/arduino/activar-riego/{$this->planta->id}/{$this->tarea->id}"), [
                    'puerto' => $this->puerto,
                    'tiempo' => $this->tiempoRiego,
                    'cantidad_ml' => $this->cantidadMl
                ]);

            if ($response->successful()) {
                $this->mensaje = '✅ Riego activado correctamente';
                $this->ultimaAccion = now()->format('H:i:s');
                $this->dispatch('riegoRegistrado');
                
                // Emitir evento para mostrar notificación
                $this->dispatchBrowserEvent('notificacion', [
                    'tipo' => 'success',
                    'mensaje' => 'Riego activado correctamente'
                ]);
            } else {
                $this->mensaje = '❌ Error: ' . $response->json()['message'];
                $this->dispatchBrowserEvent('notificacion', [
                    'tipo' => 'error',
                    'mensaje' => 'Error al activar el riego'
                ]);
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error de conexión: ' . $e->getMessage();
            $this->dispatchBrowserEvent('notificacion', [
                'tipo' => 'error',
                'mensaje' => 'Error de conexión con el servidor'
            ]);
        }
    }

    public function updated($propertyName)
    {
        // Prevenir que los cambios en los campos del modal activen otros eventos
        if ($this->mostrarConfiguracion) {
            return;
        }
    }

    public function manejarActualizacionModal($valor)
    {
        // Este método maneja los eventos emitidos cuando el modal se abre/cierra
        $this->mostrarConfiguracion = $valor;
    }

    public function actualizarEstado()
    {
        $this->verificarArduino();
    }

    public function render()
    {
        return view('livewire.riego-control');
    }
}