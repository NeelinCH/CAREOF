<?php

namespace App\Providers;

use App\Models\Planta;
use App\Models\Tarea;
use App\Models\RegistroRiego;
use App\Policies\PlantaPolicy;
use App\Policies\TareaPolicy;
use App\Policies\RegistroRiegoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Planta::class => PlantaPolicy::class,
        Tarea::class => TareaPolicy::class,
        RegistroRiego::class => RegistroRiegoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}