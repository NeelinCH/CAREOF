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
        'enviado'
    ];

    // Definir los campos de fecha usando $casts
    protected $casts = [
        'fecha_envio' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }
}