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
                return $this->registrarRiego($request, $planta, $tarea);
                
            case 'fertilizacion':
            case 'poda':
            case 'trasplante':
            case 'otro':
                return $this->registrarTareaGeneral($planta, $tarea, $request->input('observaciones'));
                
            default:
                return redirect()->back()->with('error', 'Tipo de tarea no válido');
        }
    }

    private function registrarRiego(Request $request, Planta $planta, Tarea $tarea)
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
            'cantidad_ml' => $request->cantidad_ml,
            'metodo' => $request->metodo ?? 'Manual',
            'observaciones' => $request->observaciones,
        ]);

        // Actualizar próxima fecha
        $tarea->update([
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);

        // Registrar actividad MANUALMENTE (adicional al trait)
        Actividad::create([
            'user_id' => Auth::id(),
            'planta_id' => $planta->id,
            'tipo' => 'riego',
            'descripcion' => "completó riego manual de {$request->cantidad_ml}ml en {$planta->nombre}",
            'detalles' => [
                'tarea_id' => $tarea->id,
                'registro_id' => $registro->id,
                'metodo' => $request->metodo ?? 'Manual',
                'cantidad_ml' => $request->cantidad_ml,
                'accion' => 'completar_tarea_riego'
            ]
        ]);

        return redirect()->back()->with('success', 'Riego registrado correctamente');
    }

    private function registrarTareaGeneral(Planta $planta, Tarea $tarea)
    {
        // Actualizar próxima fecha
        $tarea->update([
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);

        // Registrar actividad MANUALMENTE
        Actividad::create([
            'user_id' => Auth::id(),
            'planta_id' => $planta->id,
            'tipo' => $tarea->tipo,
            'descripcion' => "completó tarea de {$tarea->tipo} en {$planta->nombre}",
            'detalles' => [
                'tarea_id' => $tarea->id,
                'descripcion_tarea' => $tarea->descripcion,
                'frecuencia_dias' => $tarea->frecuencia_dias,
                'accion' => 'completar_tarea_general'
            ]
        ]);

        return redirect()->back()->with('success', 'Tarea marcada como completada');
    }
}