<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire; // ← IMPORTAR CORRECTAMENTE LA CLASE

class AppServiceProvider extends ServiceProvider
{
    // ELIMINA: use Livewire\Livewire; (esto está mal colocado)
    
    public function boot(): void
    {
        // Registrar componentes Livewire (solo si usas Livewire v2)
        Livewire::component('riego-control', \App\Http\Livewire\RiegoControl::class);
    }
    
    public function register(): void
    {
        //
    }
}