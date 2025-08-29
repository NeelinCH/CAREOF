<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Tarea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class TareaController extends Controller
{
    public function index(Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver las tareas de esta planta.');
        }
        
        $tareas = $planta->tareas()->orderBy('proxima_fecha')->get();
        return view('tareas.index', compact('planta', 'tareas'));
    }

    public function create(Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para crear tareas en esta planta.');
        }
        
        return view('tareas.create', compact('planta'));
    }

    public function store(Request $request, Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para crear tareas en esta planta.');
        }

        $request->validate([
            'tipo' => 'required|in:riego,fertilizacion,poda,trasplante,otro',
            'frecuencia_dias' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'proxima_fecha' => 'required|date',
        ]);

        $data = $request->all();
        $data['planta_id'] = $planta->id;
        $data['activa'] = true;

        Tarea::create($data);

        return redirect()->route('plantas.tareas.index', $planta->id)
            ->with('success', 'Tarea creada correctamente.');
    }

    public function show(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('view', $tarea)) {
            throw new AuthorizationException('No tienes permisos para ver esta tarea.');
        }
        
        return view('tareas.show', compact('planta', 'tarea'));
    }

    public function edit(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para editar esta tarea.');
        }
        
        return view('tareas.edit', compact('planta', 'tarea'));
    }

    public function update(Request $request, Planta $planta, Tarea $tarea)
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
            'tipo' => 'required|in:riego,fertilizacion,poda,trasplante,otro',
            'frecuencia_dias' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'proxima_fecha' => 'required|date',
            'activa' => 'required|boolean',
        ]);

        $tarea->update($request->all());

        return redirect()->route('plantas.tareas.index', $planta->id)
            ->with('success', 'Tarea actualizada correctamente.');
    }

    public function destroy(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('delete', $planta)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('delete', $tarea)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta tarea.');
        }

        $tarea->delete();

        return redirect()->route('plantas.tareas.index', $planta->id)
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function completar(Planta $planta, Tarea $tarea)
    {
        // Verificar autorización usando Gate para la planta
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para completar tareas en esta planta.');
        }
        
        // Verificar autorización usando Gate para la tarea
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para completar esta tarea.');
        }

        // Lógica para completar la tarea
        $tarea->update([
            'completada' => true, 
            'fecha_completada' => now(),
            'proxima_fecha' => now()->addDays($tarea->frecuencia_dias)
        ]);
        
        return redirect()->back()->with('success', 'Tarea completada correctamente');
    }
}