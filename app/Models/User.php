<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function plantas()
    {
        return $this->hasMany(Planta::class);
    }

    public function registrosRiego()
    {
        return $this->hasMany(RegistroRiego::class);
    }

    // Agregar esta relación
    public function actividades()
    {
        return $this->hasMany(Actividad::class);
    }

    // Relación para actividades recientes (opcional)
    public function actividadesRecientes($limit = 10)
    {
        return $this->actividades()
            ->with('planta')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}