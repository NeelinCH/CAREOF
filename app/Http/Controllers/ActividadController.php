<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Actividad;
use App\Models\Planta;

class ActividadController extends Controller
{
    public function index()
    {
        // Obtener actividades del usuario actual con eager loading
        $actividades = Auth::user()->actividades()
            ->with(['planta', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('actividades.index', compact('actividades'));
    }

    public function porPlanta($plantaId)
    {
        // Verificar que la planta pertenezca al usuario
        $planta = Auth::user()->plantas()->findOrFail($plantaId);
        
        // Obtener actividades de la planta específica
        $actividades = $planta->actividades()
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('actividades.planta', compact('actividades', 'planta'));
    }

    // Método adicional para dashboard (actividades recientes)
    public function recientes()
    {
        $actividades = Auth::user()->actividades()
            ->with(['planta'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($actividades);
    }
}