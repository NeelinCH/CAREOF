<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstadisticaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $estadisticas = [
            'total_plantas' => $user->plantas()->count(),
            'total_tareas' => $user->plantas()->withCount('tareas')->get()->sum('tareas_count'),
            'tareas_completadas' => $this->getTareasCompletadas(),
            'total_agua' => $this->getTotalAguaUtilizada(),
            'proxima_tarea' => $this->getProximaTarea(),
            'actividad_reciente' => $this->getActividadReciente(),
            'estadisticas_plantas' => $this->getEstadisticasPorPlanta(),
        ];

        return view('estadisticas.index', compact('estadisticas'));
    }

    private function getTareasCompletadas()
    {
        return DB::table('actividades')
            ->where('user_id', Auth::id())
            ->whereIn('tipo', ['riego', 'fertilizacion', 'poda', 'trasplante'])
            ->count();
    }

    private function getTotalAguaUtilizada()
    {
        return DB::table('registros_riego')
            ->join('tareas', 'registros_riego.tarea_id', '=', 'tareas.id')
            ->join('plantas', 'tareas.planta_id', '=', 'plantas.id')
            ->where('plantas.user_id', Auth::id())
            ->sum('registros_riego.cantidad_ml');
    }

    private function getProximaTarea()
    {
        return Tarea::whereHas('planta', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('activa', true)
            ->where('proxima_fecha', '>=', now())
            ->orderBy('proxima_fecha')
            ->with('planta')
            ->first();
    }

    private function getActividadReciente()
    {
        return Actividad::where('user_id', Auth::id())
            ->with('planta')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getEstadisticasPorPlanta()
    {
        return Planta::where('user_id', Auth::id())
            ->withCount(['tareas', 'actividades'])
            ->with(['tareas' => function($query) {
                $query->where('activa', true);
            }])
            ->get()
            ->map(function($planta) {
                return [
                    'nombre' => $planta->nombre,
                    'total_tareas' => $planta->tareas_count,
                    'tareas_activas' => $planta->tareas->count(),
                    'total_actividades' => $planta->actividades_count
                ];
            });
    }
}