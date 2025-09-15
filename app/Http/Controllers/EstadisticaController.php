<?php

namespace App\Http\Controllers;

use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstadisticaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Estadísticas básicas
        $estadisticas = [
            'total_plantas' => $user->plantas()->count(),
            'total_tareas' => $this->getTotalTareas($user),
            'tareas_completadas' => $this->getTareasCompletadas($user),
            'tareas_pendientes' => $this->getTareasPendientes($user),
            'total_agua' => $this->getTotalAguaUtilizada($user),
            'agua_este_mes' => $this->getAguaUtilizadaMes($user),
            'actividades_hoy' => $this->getActividadesHoy($user),
            'actividades_semana' => $this->getActividadesSemana($user),
            'proxima_tarea' => $this->getProximaTarea($user),
            'actividad_reciente' => $this->getActividadReciente($user),
            'estadisticas_plantas' => $this->getEstadisticasPorPlanta($user),
            'estadisticas_mensuales' => $this->getEstadisticasMensuales($user),
            'tipos_actividades' => $this->getTiposActividades($user),
            'resumen_semanal' => $this->getResumenSemanal($user)
        ];

        return view('estadisticas.index', compact('estadisticas'));
    }

    private function getTotalTareas($user)
    {
        return Tarea::whereHas('planta', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
    }

    private function getTareasCompletadas($user)
    {
        return Actividad::where('user_id', $user->id)
            ->whereIn('tipo', ['riego', 'fertilizacion', 'poda', 'trasplante', 'otro'])
            ->count();
    }

    private function getTareasPendientes($user)
    {
        return Tarea::whereHas('planta', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('activa', true)
        ->where('proxima_fecha', '<=', now()->addDays(7))
        ->count();
    }

    private function getTotalAguaUtilizada($user)
    {
        return RegistroRiego::whereHas('tarea.planta', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->sum('cantidad_ml');
    }

    private function getAguaUtilizadaMes($user)
    {
        return RegistroRiego::whereHas('tarea.planta', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->whereBetween('fecha_hora', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])
        ->sum('cantidad_ml');
    }

    private function getActividadesHoy($user)
    {
        return Actividad::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();
    }

    private function getActividadesSemana($user)
    {
        return Actividad::where('user_id', $user->id)
            ->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])
            ->count();
    }

    private function getProximaTarea($user)
    {
        return Tarea::whereHas('planta', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('activa', true)
        ->where('proxima_fecha', '>=', now())
        ->orderBy('proxima_fecha')
        ->with('planta')
        ->first();
    }

    private function getActividadReciente($user)
    {
        return Actividad::where('user_id', $user->id)
            ->with('planta')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    private function getEstadisticasPorPlanta($user)
    {
        return Planta::where('user_id', $user->id)
            ->withCount([
                'tareas',
                'actividades',
                'tareas as tareas_activas_count' => function ($query) {
                    $query->where('activa', true);
                }
            ])
            ->get()
            ->map(function($planta) {
                // Calcular agua total por planta
                $aguaTotal = RegistroRiego::whereHas('tarea', function($query) use ($planta) {
                    $query->where('planta_id', $planta->id);
                })->sum('cantidad_ml');

                return [
                    'id' => $planta->id,
                    'nombre' => $planta->nombre,
                    'especie' => $planta->especie,
                    'ubicacion' => $planta->ubicacion,
                    'total_tareas' => $planta->tareas_count,
                    'tareas_activas' => $planta->tareas_activas_count,
                    'total_actividades' => $planta->actividades_count,
                    'agua_total' => $aguaTotal,
                    'ultima_actividad' => $planta->actividades()->latest()->first()?->created_at
                ];
            });
    }

    private function getEstadisticasMensuales($user)
    {
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $mesInicio = $fecha->copy()->startOfMonth();
            $mesFin = $fecha->copy()->endOfMonth();

            $actividades = Actividad::where('user_id', $user->id)
                ->whereBetween('created_at', [$mesInicio, $mesFin])
                ->count();

            $agua = RegistroRiego::whereHas('tarea.planta', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->whereBetween('fecha_hora', [$mesInicio, $mesFin])
            ->sum('cantidad_ml');

            $meses[] = [
                'mes' => $fecha->format('M Y'),
                'actividades' => $actividades,
                'agua_ml' => $agua,
                'agua_litros' => round($agua / 1000, 2)
            ];
        }

        return $meses;
    }

    private function getTiposActividades($user)
    {
        return Actividad::where('user_id', $user->id)
            ->select('tipo', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo')
            ->orderBy('total', 'desc')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->tipo => $item->total];
            });
    }

    private function getResumenSemanal($user)
    {
        $semana = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i);
            
            $actividades = Actividad::where('user_id', $user->id)
                ->whereDate('created_at', $fecha->toDateString())
                ->count();

            $semana[] = [
                'dia' => $fecha->format('D'),
                'fecha' => $fecha->format('d/m'),
                'actividades' => $actividades
            ];
        }

        return $semana;
    }

    // Métodos adicionales para APIs (opcional)
    public function getEstadisticasJson()
    {
        $user = Auth::user();
        
        return response()->json([
            'plantas_total' => $user->plantas()->count(),
            'agua_total_litros' => round($this->getTotalAguaUtilizada($user) / 1000, 2),
            'actividades_mes' => Actividad::where('user_id', $user->id)
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->count(),
            'tareas_pendientes' => $this->getTareasPendientes($user),
            'proxima_tarea' => $this->getProximaTarea($user)
        ]);
    }

    public function exportarDatos()
    {
        $user = Auth::user();
        
        $datos = [
            'plantas' => $user->plantas()->with(['tareas', 'actividades'])->get(),
            'actividades' => $user->actividades()->with('planta')->get(),
            'registros_riego' => RegistroRiego::whereHas('tarea.planta', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->with(['tarea.planta', 'user'])->get(),
            'fecha_exportacion' => now()->toISOString()
        ];

        return response()->json($datos);
    }
}