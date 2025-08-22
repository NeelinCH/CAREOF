<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Access\AuthorizationException;

class PlantaController extends Controller
{
    public function index()
    {
        $plantas = Auth::user()->plantas()->withCount('tareas')->get();
        return view('plantas.index', compact('plantas'));
    }

    public function create()
    {
        return view('plantas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|max:255',
            'fecha_adquisicion' => 'required|date',
            'ubicacion' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except('imagen');
        $data['user_id'] = Auth::id();

        if ($request->hasFile('imagen')) {
            $path = $request->file('imagen')->store('plantas', 'public');
            $data['imagen'] = $path;
        }

        Planta::create($data);

        return redirect()->route('plantas.index')->with('success', 'Planta añadida correctamente.');
    }

    public function show(Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('view', $planta)) {
            throw new AuthorizationException('No tienes permisos para ver esta planta.');
        }
        
        $planta->load('tareas');
        return view('plantas.show', compact('planta'));
    }

    public function edit(Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }
        
        return view('plantas.edit', compact('planta'));
    }

    public function update(Request $request, Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('update', $planta)) {
            throw new AuthorizationException('No tienes permisos para editar esta planta.');
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'especie' => 'required|string|max:255',
            'fecha_adquisicion' => 'required|date',
            'ubicacion' => 'required|string|max:255',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['imagen', 'eliminar_imagen']);

        if ($request->has('eliminar_imagen') && $planta->imagen) {
            Storage::disk('public')->delete($planta->imagen);
            $data['imagen'] = null;
        }

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($planta->imagen) {
                Storage::disk('public')->delete($planta->imagen);
            }
            
            $path = $request->file('imagen')->store('plantas', 'public');
            $data['imagen'] = $path;
        }

        $planta->update($data);

        return redirect()->route('plantas.index')->with('success', 'Planta actualizada correctamente.');
    }

    public function destroy(Planta $planta)
    {
        // Verificar autorización usando Gate
        if (!Auth::user()->can('delete', $planta)) {
            throw new AuthorizationException('No tienes permisos para eliminar esta planta.');
        }

        if ($planta->imagen) {
            Storage::disk('public')->delete($planta->imagen);
        }

        $planta->delete();

        return redirect()->route('plantas.index')->with('success', 'Planta eliminada correctamente.');
    }
}