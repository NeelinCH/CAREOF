<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\RegistraActividad;

class RegistroRiego extends Model
{
    use HasFactory, RegistraActividad;

    protected $fillable = [
        'tarea_id',
        'user_id',
        'fecha_hora',
        'cantidad_ml',
        'metodo',
        'observaciones'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}