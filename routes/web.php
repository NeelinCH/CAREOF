<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\PlantaController;
use App\Http\Controllers\TareaController;
use App\Http\Controllers\RegistroRiegoController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\ArduinoController;

Route::get('/', function () {
    return view('welcome');
});

// ================= RUTAS PÚBLICAS (GUEST) =================
Route::middleware('guest')->group(function () {
    // Ruta de registro - SIMPLIFICADA
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

    // Ruta de login - SIMPLIFICADA
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
    // Ruta de logout - SIMPLIFICADA
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Sesión cerrada correctamente');
    })->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas para Plantas
    Route::resource('plantas', PlantaController::class);
    
    // Actividades
    Route::get('actividades', [ActividadController::class, 'index'])->name('actividades.index');
    Route::get('plantas/{planta}/actividades', [ActividadController::class, 'porPlanta'])->name('plantas.actividades');
    
    // Arduino
    Route::post('plantas/{planta}/tareas/{tarea}/activar-riego', [ArduinoController::class, 'activarRiego'])->name('arduino.activar-riego');
    Route::get('api/arduino/estado', [ArduinoController::class, 'estadoArduino'])->name('arduino.estado');
    
    // Rutas anidadas para Tareas
    Route::prefix('plantas/{planta}')->group(function () {
        Route::get('tareas', [TareaController::class, 'index'])->name('plantas.tareas.index');
        Route::get('tareas/create', [TareaController::class, 'create'])->name('plantas.tareas.create');
        Route::post('tareas', [TareaController::class, 'store'])->name('plantas.tareas.store');
        
        Route::prefix('tareas/{tarea}')->group(function () {
            Route::get('/', [TareaController::class, 'show'])->name('plantas.tareas.show');
            Route::get('edit', [TareaController::class, 'edit'])->name('plantas.tareas.edit');
            Route::put('/', [TareaController::class, 'update'])->name('plantas.tareas.update');
            Route::delete('/', [TareaController::class, 'destroy'])->name('plantas.tareas.destroy');
            
            // Registros de riego
            Route::prefix('registros')->group(function () {
                Route::get('/', [RegistroRiegoController::class, 'index'])->name('plantas.tareas.registros.index');
                Route::get('create', [RegistroRiegoController::class, 'create'])->name('plantas.tareas.registros.create');
                Route::post('/', [RegistroRiegoController::class, 'store'])->name('plantas.tareas.registros.store');
                
                Route::prefix('{registroRiego}')->group(function () {
                    Route::get('/', [RegistroRiegoController::class, 'show'])->name('plantas.tareas.registros.show');
                    Route::delete('/', [RegistroRiegoController::class, 'destroy'])->name('plantas.tareas.registros.destroy');
                });
            });
        });
    });
});