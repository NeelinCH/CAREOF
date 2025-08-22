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
        $usuario = $this->user->name;
        $planta = $this->planta->nombre;
        
        return "{$usuario} {$this->descripcion} {$planta}";
    }

    // Método para obtener el icono según el tipo
    public function getIconoAttribute()
    {
        $iconos = [
            'riego' => 'fa-tint',
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
            'planta' => 'green',
            'tarea' => 'yellow',
            'sistema' => 'gray'
        ];

        return $colores[$this->tipo] ?? 'gray';
    }
}