<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use App\Models\Planta;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\AuthorizationException;

class TareaController extends Controller
{
    public function index(Planta $planta)
    {
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver las tareas de esta planta.');
        }
        
        $tareas = $planta->tareas()->orderBy('proxima_fecha')->get();
        return view('tareas.index', compact('planta', 'tareas'));
    }

    public function create(Planta $planta)
    {
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para crear tareas en esta planta.');
        }
        
        return view('tareas.create', compact('planta'));
    }

    public function store(Request $request, Planta $planta)
    {
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
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver esta planta.');
        }
        
        if (!Auth::user()->can('view', $tarea)) {
            throw new AuthorizationException('No tienes permisos para ver esta tarea.');
        }
        
        // Cargar registros de riego si es una tarea de riego
        $registrosRecientes = [];
        if ($tarea->tipo === 'riego') {
            $registrosRecientes = $tarea->registrosRiego()
                ->with('user')
                ->orderBy('fecha_hora', 'desc')
                ->take(5)
                ->get();
        }
        
        return view('tareas.show', compact('planta', 'tarea', 'registrosRecientes'));
    }

    public function edit(Planta $planta, Tarea $tarea)
    {
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para editar esta tarea.');
        }
        
        return view('tareas.edit', compact('planta', 'tarea'));
    }

    public function update(Request $request, Planta $planta, Tarea $tarea)
    {
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
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
        if (!Auth::user()->can('delete', $planta)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta planta.');
        }
        
        if (!Auth::user()->can('delete', $tarea)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta tarea.');
        }

        $tarea->delete();

        return redirect()->route('plantas.tareas.index', $planta->id)
            ->with('success', 'Tarea eliminada correctamente.');
    }

    public function completar(Planta $planta, Tarea $tarea)
    {
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para completar tareas en esta planta.');
        }
        
        if (!Auth::user()->can('update', $tarea)) {
            throw new AuthorizationException('No tienes permisos para completar esta tarea.');
        }

        // Redirigir al controlador especializado para completar tareas
        return redirect()->route('plantas.tareas.completar.store', [$planta->id, $tarea->id]);
    }
}