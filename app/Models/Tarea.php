<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraActividad;

class Tarea extends Model
{
    use HasFactory, RegistraActividad;

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
        return $this->hasMany(RegistroRiego::class);
    }

    public function recordatorios()
    {
        return $this->hasMany(Recordatorio::class);
    }
}