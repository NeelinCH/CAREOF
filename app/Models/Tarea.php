<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'planta_id',
        'tipo',
        'frecuencia_dias',
        'descripcion',
        'proxima_fecha',
        'activa'
    ];

    protected $casts = [
        'proxima_fecha' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    public function registrosRiego()
    {
        return $this->hasMany(RegistroRiego::class, 'tarea_id');
    }

    public function recordatorios()
    {
        return $this->hasMany(Recordatorio::class);
    }

    /**
     * Crear un recordatorio automáticamente para la tarea
     */
    public function crearRecordatorio()
    {
        if ($this->proxima_fecha && $this->activa) {
            return Recordatorio::create([
                'tarea_id' => $this->id,
                'fecha_envio' => $this->proxima_fecha,
                'tipo_recordatorio' => 'email',
                'enviado' => false
            ]);
        }
        return null;
    }

    /**
     * Boot method para eventos del modelo
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($tarea) {
            $tarea->crearRecordatorio();
        });

        static::updated(function ($tarea) {
            if ($tarea->isDirty('proxima_fecha') || $tarea->isDirty('activa')) {
                // Eliminar recordatorios pendientes existentes
                $tarea->recordatorios()
                    ->where('enviado', false)
                    ->delete();
                    
                // Crear nuevo recordatorio si está activa
                if ($tarea->activa && $tarea->proxima_fecha) {
                    $tarea->crearRecordatorio();
                }
            }
        });

        static::deleting(function ($tarea) {
            // Eliminar todos los recordatorios asociados al eliminar la tarea
            $tarea->recordatorios()->delete();
        });
    }
}