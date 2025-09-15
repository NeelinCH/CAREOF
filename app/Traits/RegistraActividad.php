<?php

namespace App\Traits;

use App\Models\Actividad;
use Illuminate\Support\Facades\Auth;

trait RegistraActividad
{
    protected static function bootRegistraActividad()
    {
        static::created(function ($model) {
            $model->registrarActividad('created');
        });

        static::updated(function ($model) {
            $model->registrarActividad('updated');
        });

        static::deleted(function ($model) {
            $model->registrarActividad('deleted');
        });
    }

    protected function registrarActividad($event)
    {
        // Solo registrar si hay un usuario autenticado
        if (!Auth::check()) {
            return;
        }

        $descripciones = [
            'created' => 'creó',
            'updated' => 'actualizó', 
            'deleted' => 'eliminó'
        ];

        $tipoMap = [
            'Planta' => 'planta',
            'Tarea' => 'tarea',
            'RegistroRiego' => 'riego'
        ];

  if ($event === 'completed') {
        $descripciones['completed'] = 'completó';
    }

        $modelClass = class_basename($this);
        
        // Obtener el ID de la planta según el tipo de modelo
        $plantaId = null;
        
        if ($modelClass === 'Planta') {
            $plantaId = $this->id;
        } elseif ($modelClass === 'Tarea') {
            $plantaId = $this->planta_id;
        } elseif ($modelClass === 'RegistroRiego') {
            // Para RegistroRiego necesitamos obtener la planta a través de la tarea
            if (isset($this->tarea_id) && $this->tarea_id) {
                $tarea = \App\Models\Tarea::find($this->tarea_id);
                $plantaId = $tarea ? $tarea->planta_id : null;
            }
        }

        // Solo registrar si tenemos planta_id
        if (!$plantaId) {
            return;
        }

        try {
            Actividad::create([
                'user_id' => Auth::id(),
                'planta_id' => $plantaId,
                'tipo' => $tipoMap[$modelClass] ?? 'sistema',
                'descripcion' => "{$descripciones[$event]} " . $this->getNombreParaActividad(),
                'detalles' => [
                    'modelo' => $modelClass,
                    'evento' => $event,
                    'modelo_id' => $this->id,
                    'datos' => $this->getAtributosParaActividad()
                ]
            ]);
        } catch (\Exception $e) {
            // Log del error sin interrumpir el flujo principal
            \Log::error('Error registrando actividad: ' . $e->getMessage(), [
                'modelo' => $modelClass,
                'evento' => $event,
                'user_id' => Auth::id(),
                'planta_id' => $plantaId
            ]);
        }
    }

    /**
     * Obtiene el nombre descriptivo para la actividad
     */
    protected function getNombreParaActividad()
    {
        $modelClass = class_basename($this);
        
        switch ($modelClass) {
            case 'Planta':
                return "la planta \"{$this->nombre}\"";
            case 'Tarea':
                return "la tarea de {$this->tipo}";
            case 'RegistroRiego':
                return "un registro de riego";
            default:
                return strtolower($modelClass);
        }
    }

    /**
     * Obtiene los atributos específicos para la actividad
     */
    protected function getAtributosParaActividad()
    {
        $modelClass = class_basename($this);
        
        switch ($modelClass) {
            case 'Planta':
                return [
                    'nombre' => $this->nombre,
                    'especie' => $this->especie,
                    'ubicacion' => $this->ubicacion
                ];
            case 'Tarea':
                return [
                    'tipo' => $this->tipo,
                    'frecuencia_dias' => $this->frecuencia_dias,
                    'proxima_fecha' => $this->proxima_fecha
                ];
            case 'RegistroRiego':
                return [
                    'cantidad_ml' => $this->cantidad_ml,
                    'metodo' => $this->metodo,
                    'fecha_hora' => $this->fecha_hora
                ];
            default:
                return $this->toArray();
        }
    }
}