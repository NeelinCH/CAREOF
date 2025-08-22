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

        // Obtener el ID de la planta de diferentes maneras según el modelo
        $plantaId = $this->planta_id ?? 
                   (isset($this->tarea) ? $this->tarea->planta_id : null) ??
                   (isset($this->planta) ? $this->planta->id : null);

        // Solo registrar si tenemos un usuario autenticado y planta ID
        if (Auth::check() && $plantaId) {
            Actividad::create([
                'user_id' => Auth::id(),
                'planta_id' => $plantaId,
                'tipo' => $tipoMap[class_basename($this)] ?? 'sistema',
                'descripcion' => "{$descripciones[$event]} " . strtolower(class_basename($this)),
                'detalles' => [
                    'modelo' => class_basename($this),
                    'evento' => $event,
                    'datos' => $this->toArray()
                ]
            ]);
        }
    }
}