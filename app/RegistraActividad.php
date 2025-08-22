<?php

namespace App\Traits;

use App\Models\Actividad;

trait RegistraActividad
{
    protected static function bootRegistraActividad()
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::$event(function ($model) use ($event) {
                $model->registrarActividad($event);
            });
        }
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

        Actividad::create([
            'user_id' => auth()->id(),
            'planta_id' => $this->planta_id ?? ($this->tarea->planta_id ?? null),
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