<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraActividad;

class Planta extends Model
{
    use HasFactory, RegistraActividad;

    protected $fillable = [
        'user_id',
        'nombre',
        'especie',
        'fecha_adquisicion',
        'ubicacion',
        'imagen'
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tareas()
    {
        return $this->hasMany(Tarea::class);
    }

    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }
}