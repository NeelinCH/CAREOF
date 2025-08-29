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
        // Obtener todas las actividades del usuario con eager loading
        $actividades = Auth::user()->actividades()
            ->with(['planta', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Estadísticas resumen
        $estadisticas = [
            'total' => $actividades->total(),
            'hoy' => Auth::user()->actividades()->whereDate('created_at', today())->count(),
            'esta_semana' => Auth::user()->actividades()->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'este_mes' => Auth::user()->actividades()->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('actividades.index', compact('actividades', 'estadisticas'));
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

        // Estadísticas de la planta
        $estadisticas = [
            'total' => $actividades->total(),
            'riego' => $planta->actividades()->where('tipo', 'riego')->count(),
            'poda' => $planta->actividades()->where('tipo', 'poda')->count(),
            'fertilizacion' => $planta->actividades()->where('tipo', 'fertilizacion')->count(),
            'trasplante' => $planta->actividades()->where('tipo', 'trasplante')->count(),
        ];

        return view('actividades.planta', compact('actividades', 'planta', 'estadisticas'));
    }

    public function porTipo($tipo)
    {
        $tiposValidos = ['riego', 'poda', 'fertilizacion', 'trasplante', 'otro'];
        
        if (!in_array($tipo, $tiposValidos)) {
            abort(404, 'Tipo de actividad no válido');
        }

        $actividades = Auth::user()->actividades()
            ->where('tipo', $tipo)
            ->with(['planta', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('actividades.tipo', compact('actividades', 'tipo'));
    }
}