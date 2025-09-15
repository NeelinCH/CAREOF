<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Planta;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TareaCompletadaController extends Controller
{
    public function store(Request $request, Planta $planta, Tarea $tarea)
    {
        // Verificar autorización
        if (!Auth::user()->can('update', $planta) || !Auth::user()->can('update', $tarea)) {
            abort(403, 'No autorizado');
        }

        // Procesar según el tipo de tarea
        switch ($tarea->tipo) {
            case 'riego':
                return $this->registrarRiego($request, $tarea);
                
            case 'fertilizacion':
            case 'poda':
            case 'trasplante':
            case 'otro':
                return $this->registrarTareaGeneral($tarea);
                
            default:
                return redirect()->back()->with('error', 'Tipo de tarea no válido');
        }
    }

    private function registrarRiego(Request $request, Tarea $tarea)
    {
        $request->validate([
            'cantidad_ml' => 'nullable|integer|min:1',
            'metodo' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        // Crear registro de riego
        $registro = RegistroRiego::create([
            'tarea_id' => $tarea->id,
            'user_id' => Auth::id(),
            'fecha_hora' => now(),
            'cantidad_ml' => $request->cantidad_ml ?? 0,
            'metodo' => $request->metodo ?? 'Manual',
            'observaciones' => $request->observaciones,
        ]);

        // Actualizar la fecha de última ejecución y calcular próxima fecha
        $tarea->update([
            'ultima_ejecucion' => now(),
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);

        // Registrar actividad
        Actividad::create([
            'user_id' => Auth::id(),
            'planta_id' => $tarea->planta_id,
            'tipo' => 'riego',
            'descripcion' => "realizó riego de {$request->cantidad_ml}ml",
            'detalles' => [
                'tarea_id' => $tarea->id,
                'registro_id' => $registro->id,
                'metodo' => $request->metodo ?? 'Manual',
                'cantidad_ml' => $request->cantidad_ml ?? 0,
                'observaciones' => $request->observaciones
            ]
        ]);

        return redirect()->back()->with('success', 'Riego registrado correctamente');
    }

    private function registrarTareaGeneral(Tarea $tarea)
    {
        // Actualizar la fecha de última ejecución y calcular próxima fecha
        $tarea->update([
            'ultima_ejecucion' => now(),
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);

        // Registrar actividad según el tipo de tarea
        Actividad::create([
            'user_id' => Auth::id(),
            'planta_id' => $tarea->planta_id,
            'tipo' => $tarea->tipo,
            'descripcion' => "completó tarea de {$tarea->tipo}",
            'detalles' => [
                'tarea_id' => $tarea->id,
                'descripcion' => $tarea->descripcion,
                'tipo_tarea' => $tarea->tipo,
                'frecuencia_dias' => $tarea->frecuencia_dias
            ]
        ]);

        return redirect()->back()->with('success', 'Tarea marcada como completada');
    }
}