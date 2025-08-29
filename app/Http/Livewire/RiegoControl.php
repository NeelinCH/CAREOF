<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Tarea;
use App\Models\Planta;
use Illuminate\Support\Facades\Http;

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

    protected $listeners = ['riegoActivado' => 'actualizarEstado'];

    public function mount(Planta $planta, Tarea $tarea)
    {
        $this->planta = $planta;
        $this->tarea = $tarea;
        $this->verificarArduino();
        
        // Cargar configuración guardada
        $this->puerto = session('arduino_puerto', 'COM9');
        $this->tiempoRiego = session('arduino_tiempo_riego', 2000);
        $this->cantidadMl = session('arduino_cantidad_ml', 500);
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

    public function activarRiego()
    {
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->post(url("/api/plantas/{$this->planta->id}/tareas/{$this->tarea->id}/activar-riego"), [
                    'puerto' => $this->puerto,
                    'tiempo' => $this->tiempoRiego,
                    'cantidad_ml' => $this->cantidadMl
                ]);

            if ($response->successful()) {
                $this->mensaje = '✅ Riego activado correctamente';
                $this->ultimaAccion = now()->format('H:i:s');
                $this->emit('riegoRegistrado');
            } else {
                $this->mensaje = '❌ Error: ' . $response->json()['message'];
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error de conexión: ' . $e->getMessage();
        }
    }

    public function guardarConfiguracion()
    {
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->post(url('/api/arduino/configuracion'), [
                    'puerto' => $this->puerto,
                    'tiempo_riego' => $this->tiempoRiego,
                    'cantidad_ml' => $this->cantidadMl
                ]);

            if ($response->successful()) {
                $this->mensaje = '✅ Configuración guardada';
                $this->mostrarConfiguracion = false;
                
                // Guardar en sesión
                session([
                    'arduino_puerto' => $this->puerto,
                    'arduino_tiempo_riego' => $this->tiempoRiego,
                    'arduino_cantidad_ml' => $this->cantidadMl
                ]);
            }
        } catch (\Exception $e) {
            $this->mensaje = '❌ Error guardando configuración';
        }
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