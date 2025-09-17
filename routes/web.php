<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\PlantaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\TareaCompletadaController;
use App\Http\Controllers\RegistroRiegoController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\ArduinoController;
use App\Http\Controllers\ArduinoConfigController;
use App\Http\Controllers\ProfileController;

// ================= RUTA PÚBLICA PRINCIPAL =================
Route::get('/', function () {
    return view('welcome');
});

// ================= RUTAS PÚBLICAS (GUEST) =================
Route::middleware('guest')->group(function () {
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    Route::post('/register', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        return redirect()->route('plantas.index')->with('success', '¡Cuenta creada exitosamente!');
    });

    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');

    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('plantas.index'))->with('success', '¡Bienvenido de vuelta!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ]);
    });
});

// ================= RUTAS PROTEGIDAS (AUTH) =================
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    })->name('logout');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Estadísticas
    Route::get('/estadisticas', [EstadisticaController::class, 'index'])->name('estadisticas.index');
    Route::get('/api/estadisticas/json', [EstadisticaController::class, 'getEstadisticasJson'])->name('estadisticas.json');
    Route::get('/estadisticas/exportar', [EstadisticaController::class, 'exportarDatos'])->name('estadisticas.exportar');

    // Plantas
    Route::resource('plantas', PlantaController::class);

    // Actividades - RUTAS ACTUALIZADAS (MANTENER COMPATIBILIDAD)
    Route::get('/actividades', [ActividadController::class, 'index'])->name('actividades.index');
    Route::get('/actividades/tipo/{tipo}', [ActividadController::class, 'filtrarPorTipo'])->name('actividades.filtrar'); // Nueva ruta para filtros
    Route::get('/actividades/por-tipo/{tipo}', [ActividadController::class, 'porTipo'])->name('actividades.tipo'); // Mantener ruta existente para compatibilidad
    Route::get('/plantas/{planta}/actividades', [ActividadController::class, 'porPlanta'])->name('plantas.actividades');

    // Arduino configuración
    Route::prefix('arduino')->name('arduino.')->group(function () {
        // Configuración existente
        Route::get('/config', [ArduinoConfigController::class, 'index'])->name('config');
        Route::post('/config/save', [ArduinoConfigController::class, 'saveConfig'])->name('config.save');
        Route::post('/config/test', [ArduinoConfigController::class, 'testConnection'])->name('config.test');
        
        // Rutas existentes
        Route::get('/verificar', [ArduinoController::class, 'verificarConexionWeb'])->name('verificar');
        Route::post('/test', [ArduinoController::class, 'testComunicacionWeb'])->name('test');
        Route::get('/puertos', [ArduinoController::class, 'escanearPuertosWeb'])->name('puertos');
        
        // Rutas adicionales existentes
        Route::get('/escanear-puertos', [ArduinoController::class, 'escanearPuertosWeb'])->name('escanear-puertos');
        Route::get('/verificar-conexion', [ArduinoController::class, 'verificarConexionWeb'])->name('verificar-conexion');
        
        // Rutas existentes adicionales
        Route::post('/config/guardar', [ArduinoConfigController::class, 'saveConfig'])->name('config.guardar');
        Route::post('/configuracion', [ArduinoController::class, 'guardarConfiguracion'])->name('configuracion');
        
        // NUEVAS RUTAS PARA SOPORTAR MÚLTIPLES PUERTOS (AÑADIDAS)
        Route::get('/puertos-extendidos', [ArduinoController::class, 'obtenerPuertosExtendidosWeb'])
            ->name('puertos-extendidos');
        Route::post('/verificar-multi', [ArduinoController::class, 'verificarConexionMultiWeb'])
            ->name('verificar-multi');
    });

    // Arduino riego
    Route::post('/plantas/{planta}/tareas/{tarea}/activar-riego', [ArduinoController::class, 'activarRiego'])
        ->name('arduino.activar-riego');
    Route::get('/api/arduino/estado', [ArduinoController::class, 'estadoArduino'])
        ->name('arduino.estado');

    // Tareas y registros (anidadas en plantas)
    Route::prefix('plantas/{planta}')->name('plantas.')->group(function () {
        Route::get('/tareas', [TareaController::class, 'index'])->name('tareas.index');
        Route::get('/tareas/create', [TareaController::class, 'create'])->name('tareas.create');
        Route::post('/tareas', [TareaController::class, 'store'])->name('tareas.store');

        Route::prefix('tareas/{tarea}')->name('tareas.')->group(function () {
            Route::get('/', [TareaController::class, 'show'])->name('show');
            Route::get('/edit', [TareaController::class, 'edit'])->name('edit');
            Route::put('/', [TareaController::class, 'update'])->name('update');
            Route::delete('/', [TareaController::class, 'destroy'])->name('destroy');

            // Completar tareas (RUTA AÑADIDA)
            Route::patch('/completar', [TareaController::class, 'completar'])->name('completar');
            Route::post('/completar', [TareaCompletadaController::class, 'store'])->name('completar.store');

            // Registros de riego
            Route::prefix('registros')->name('registros.')->group(function () {
                Route::get('/', [RegistroRiegoController::class, 'index'])->name('index');
                Route::get('/create', [RegistroRiegoController::class, 'create'])->name('create');
                Route::post('/', [RegistroRiegoController::class, 'store'])->name('store');

                Route::prefix('{registroRiego}')->group(function () {
                    Route::get('/', [RegistroRiegoController::class, 'show'])->name('show');
                    Route::get('/edit', [RegistroRiegoController::class, 'edit'])->name('edit'); // RUTA AÑADIDA
                    Route::put('/', [RegistroRiegoController::class, 'update'])->name('update'); // RUTA AÑADIDA
                    Route::delete('/', [RegistroRiegoController::class, 'destroy'])->name('destroy');
                });
            });
        });
    });

    // RUTAS ADICIONALES AÑADIDAS
    Route::patch('/tareas/{tarea}/completar', [TareaController::class, 'completar'])->name('tareas.completar');
    Route::get('/plantas/{planta}/tareas/{tarea}', [TareaController::class, 'show'])->name('plantas.tareas.show');
    Route::get('/plantas/{planta}/tareas/{tarea}/edit', [TareaController::class, 'edit'])->name('plantas.tareas.edit');
    Route::delete('/plantas/{planta}/tareas/{tarea}', [TareaController::class, 'destroy'])->name('plantas.tareas.destroy');
    Route::get('/plantas/{planta}/tareas/create', [TareaController::class, 'create'])->name('plantas.tareas.create');
});