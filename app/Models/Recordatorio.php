<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recordatorio extends Model
{
    use HasFactory;

    protected $fillable = [
        'tarea_id',
        'fecha_envio',
        'enviado',
        'tipo_recordatorio',
        'detalles',
        'enviado_at',
        'error_mensaje',
        'intentos'
    ];

    protected $casts = [
        'fecha_envio' => 'datetime',
        'enviado_at' => 'datetime',
        'detalles' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }
}