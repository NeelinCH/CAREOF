<?php

namespace App\Http\Controllers;

use App\Models\Actividad;
use App\Models\Planta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActividadController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tipo = $request->get('tipo');
        
        // Obtener actividades con filtro opcional por tipo
        $query = Actividad::with(['user', 'planta'])
            ->where('user_id', $user->id);
            
        if ($tipo) {
            $query->where('tipo', $tipo);
        }
        
        $actividades = $query->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Calcular estadísticas para la vista de actividades
        $estadisticas = $this->calcularEstadisticas($user);

        return view('actividades.index', compact('actividades', 'estadisticas'));
    }

    public function filtrarPorTipo($tipo)
    {
        $user = Auth::user();
        
        $actividades = Actividad::with(['user', 'planta'])
            ->where('user_id', $user->id)
            ->where('tipo', $tipo)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Calcular estadísticas para la vista de actividades
        $estadisticas = $this->calcularEstadisticas($user);

        return view('actividades.index', compact('actividades', 'estadisticas'));
    }

    // Método para mantener compatibilidad con rutas existentes
    public function porTipo($tipo)
    {
        // Redirigir a la nueva ruta para mantener funcionalidad
        return redirect()->route('actividades.filtrar', ['tipo' => $tipo]);
    }

    public function porPlanta($plantaId)
    {
        $user = Auth::user();
        $planta = Planta::where('id', $plantaId)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        $actividades = Actividad::with(['user', 'planta'])
            ->where('user_id', $user->id)
            ->where('planta_id', $plantaId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Calcular estadísticas para la vista de actividades
        $estadisticas = $this->calcularEstadisticas($user);

        return view('actividades.index', compact('actividades', 'estadisticas', 'planta'));
    }

    // Método privado para calcular estadísticas (evitar duplicación de código)
    private function calcularEstadisticas($user)
    {
        return [
            'total' => Actividad::where('user_id', $user->id)->count(),
            'hoy' => Actividad::where('user_id', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            'esta_semana' => Actividad::where('user_id', $user->id)
                ->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count(),
            'este_mes' => Actividad::where('user_id', $user->id)
                ->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
                ->count()
        ];
    }
}