<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    // Especificar el nombre de la tabla
    protected $table = 'actividades';

    protected $fillable = [
        'user_id',
        'planta_id',
        'tipo',
        'descripcion',
        'detalles'
    ];

    protected $casts = [
        'detalles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    // Método para obtener la descripción formateada
    public function getDescripcionCompletaAttribute()
    {
        $usuario = $this->user->name ?? 'Usuario desconocido';
        $planta = $this->planta->nombre ?? 'Planta desconocida';
        
        return "{$usuario} {$this->descripcion} - {$planta}";
    }

    // Método para obtener el icono según el tipo
    public function getIconoAttribute()
    {
        $iconos = [
            'riego' => 'fa-tint',
            'fertilizacion' => 'fa-flask',
            'poda' => 'fa-cut',
            'trasplante' => 'fa-seedling',
            'planta' => 'fa-leaf',
            'tarea' => 'fa-tasks',
            'sistema' => 'fa-cog'
        ];

        return $iconos[$this->tipo] ?? 'fa-circle';
    }

    // Método para obtener el color según el tipo
    public function getColorAttribute()
    {
        $colores = [
            'riego' => 'blue',
            'fertilizacion' => 'yellow',
            'poda' => 'green', 
            'trasplante' => 'purple',
            'planta' => 'green',
            'tarea' => 'indigo',
            'sistema' => 'gray'
        ];

        return $colores[$this->tipo] ?? 'gray';
    }

    // Scopes útiles para consultas
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorPlanta($query, $plantaId)
    {
        return $query->where('planta_id', $plantaId);
    }

    public function scopeRecientes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }
}