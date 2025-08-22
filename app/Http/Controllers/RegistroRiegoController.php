<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class RegistroRiegoController extends Controller
{
    public function index(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('view', $tarea)) {
            throw new AuthorizationException('No tienes permisos para ver esta tarea.');
        }
        
        $registros = $tarea->registrosRiego()->orderBy('fecha_hora', 'desc')->get();
        return view('registros-riego.index', compact('planta', 'tarea', 'registros'));
    }

    public function create(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para editar esta tarea.');
        }
        
        return view('registros-riego.create', compact('planta', 'tarea'));
    }

    public function store(Request $request, Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para editar esta tarea.');
        }

        $request->validate([
            'cantidad_ml' => 'nullable|integer|min:1',
            'metodo' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
            'fecha_hora' => 'required|date',
        ]);

        $data = $request->all();
        $data['tarea_id'] = $tarea->id;
        $data['user_id'] = Auth::id();

        // Convertir la fecha/hora a formato adecuado
        if ($request->has('fecha_hora')) {
            $data['fecha_hora'] = \Carbon\Carbon::parse($request->fecha_hora);
        }

        RegistroRiego::create($data);

        // Actualizar la próxima fecha de la tarea
        $tarea->update([
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);

        return redirect()->route('plantas.tareas.registros.index', [$planta->id, $tarea->id])
            ->with('success', 'Registro de riego añadido correctamente.');
    }

    public function show(Planta $planta, Tarea $tarea, RegistroRiego $registroRiego)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('view', $tarea)) {
            throw new AuthorizationException('No tienes permisos para ver esta tarea.');
        }
        
        // Verificar autorización usando Gate para el registro
        if (!Auth::user()->can('view', $registroRiego)) {
            throw new AuthorizationException('No tienes permisos para ver este registro.');
        }
        
        return view('registros-riego.show', compact('planta', 'tarea', 'registroRiego'));
    }

    public function destroy(Planta $planta, Tarea $tarea, RegistroRiego $registroRiego)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('delete', $planta)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('delete', $tarea)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta tarea.');
        }
        
        // Verificar autorización usando Gate para el registro
        if (!Auth::user()->can('delete', $registroRiego)) {
            throw new AuthorizationException('No tienes permisos para eliminar este registro.');
        }

        $registroRiego->delete();

        return redirect()->route('plantas.tareas.registros.index', [$planta->id, $tarea->id])
            ->with('success', 'Registro de riego eliminado correctamente.');
    }
}