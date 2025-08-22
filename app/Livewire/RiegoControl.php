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
    public $cantidadMl = 500;
    public $mensaje = '';
    public $mostrarConfirmacion = false;
    public $estadoArduino = 'Desconocido';
    
    protected $listeners = ['riegoActivado' => 'actualizarEstado'];

    public function mount(Planta $planta, Tarea $tarea)
    {
        $this->planta = $planta;
        $this->tarea = $tarea;
        $this->verificarArduino();
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
        $this->mostrarConfirmacion = true;
    }

    public function confirmarRiego()
    {
        $this->mostrarConfirmacion = false;
        
        try {
            $response = Http::withToken(auth()->user()->currentAccessToken()->token ?? '')
                ->post(url("/api/plantas/{$this->planta->id}/tareas/{$this->tarea->id}/activar-riego"), [
                    'puerto' => $this->puerto,
                    'cantidad_ml' => $this->cantidadMl
                ]);

            if ($response->successful()) {
                $this->mensaje = 'Riego activado correctamente';
                $this->emit('riegoRegistrado');
            } else {
                $this->mensaje = 'Error: ' . $response->json()['message'];
            }
        } catch (\Exception $e) {
            $this->mensaje = 'Error de conexión: ' . $e->getMessage();
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