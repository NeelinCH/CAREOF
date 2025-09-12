<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de Tarea') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header con navegación y acciones -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <a href="{{ route('plantas.tareas.index', $planta->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i> Volver a tareas
                    </a>
                    <h3 class="text-lg font-medium mt-2">Detalles de la Tarea</h3>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('plantas.tareas.edit', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <i class="fas fa-edit mr-2"></i> Editar
                    </a>
                    <form action="{{ route('plantas.tareas.destroy', [$planta->id, $tarea->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                            <i class="fas fa-trash mr-2"></i> Eliminar
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna 1: Información principal -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tarjeta de información de la tarea -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h4 class="font-bold text-lg mb-4 text-gray-800">Información de la Tarea</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Tipo de tarea</p>
                                    <div class="flex items-center">
                                        @switch($tarea->tipo)
                                            @case('riego')
                                                <i class="fas fa-tint text-blue-500 mr-2"></i>
                                                @break
                                            @case('fertilizacion')
                                                <i class="fas fa-flask text-green-500 mr-2"></i>
                                                @break
                                            @case('poda')
                                                <i class="fas fa-cut text-yellow-500 mr-2"></i>
                                                @break
                                            @default
                                                <i class="fas fa-tasks text-gray-500 mr-2"></i>
                                        @endswitch
                                        <span class="capitalize">{{ $tarea->tipo }}</span>
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Frecuencia</p>
                                    <p class="text-sm text-gray-900">Cada {{ $tarea->frecuencia_dias }} días</p>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Próxima fecha</p>
                                    <div class="flex items-center">
                                        <p class="text-sm text-gray-900 mr-2">
                                            {{ $tarea->proxima_fecha ? $tarea->proxima_fecha->format('d/m/Y') : 'N/A' }}
                                        </p>
                                        @if($tarea->proxima_fecha && $tarea->proxima_fecha->isToday())
                                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Hoy</span>
                                        @elseif($tarea->proxima_fecha && $tarea->proxima_fecha->isPast())
                                            <span class="px-2 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full">Atrasado</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Estado</p>
                                    <p class="text-sm">
                                        @if($tarea->activa)
                                            <span class="px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full">Activa</span>
                                        @else
                                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Inactiva</span>
                                        @endif
                                    </p>
                                </div>
                                
                                <div class="md:col-span-2 space-y-1">
                                    <p class="text-sm font-medium text-gray-500">Descripción</p>
                                    <p class="text-sm text-gray-900">{{ $tarea->descripcion ?? 'Sin descripción' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de completar tarea -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h4 class="font-bold text-lg mb-4 text-gray-800">Completar Tarea</h4>
                            
                            @if($tarea->tipo === 'riego')
                            <form action="{{ route('plantas.tareas.completar', [$planta->id, $tarea->id]) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad (ml)</label>
                                        <input type="number" name="cantidad_ml" min="1" value="500" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Método</label>
                                        <select name="metodo" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="Regadera">Regadera</option>
                                            <option value="Manguera">Manguera</option>
                                            <option value="Aspersores">Aspersores</option>
                                            <option value="Goteo">Goteo</option>
                                            <option value="Manual">Manual</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                        <input type="text" name="observaciones" placeholder="Opcional..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-check-circle mr-2"></i> Marcar Riego Completado
                                </button>
                            </form>
                            @else
                            <!-- PARA OTROS TIPOS DE TAREA COMO PODA -->
                            <form action="{{ route('plantas.tareas.completar', [$planta->id, $tarea->id]) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones (opcional)</label>
                                    <textarea name="observaciones" rows="3" placeholder="Describe cómo fue realizada la {{ $tarea->tipo }}..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                                </div>
                                <p class="text-sm text-gray-600 mb-4">¿Has completado esta tarea de {{ $tarea->tipo }}?</p>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-check-circle mr-2"></i> Marcar como Completada
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>

                    <!-- Control de Riego Arduino (solo para tareas de riego) -->
                    @if($tarea->tipo === 'riego')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            @livewire('riego-control', ['planta' => $planta, 'tarea' => $tarea])
                        </div>
                    </div>
                    @endif

                    <!-- Registros de Riego (solo para tareas de riego) -->
                    @if($tarea->tipo === 'riego')
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-2">
                                <h4 class="font-bold text-lg text-gray-800">Registros de Riego</h4>
                                <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-2"></i> Nuevo Registro
                                </a>
                            </div>

                            @if($tarea->registrosRiego->isEmpty())
                                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-blue-700">
                                                No hay registros de riego para esta tarea. <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" class="font-medium underline text-blue-700 hover:text-blue-600">Registra un riego</a>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="space-y-3 max-h-96 overflow-y-auto pr-2 scrollbar-thin">
                                    @foreach($tarea->registrosRiego->sortByDesc('fecha_hora') as $registro)
                                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors">
                                            <div class="flex flex-col sm:flex-row justify-between items-start gap-3">
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-1">
                                                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $registro->fecha_hora ? $registro->fecha_hora->format('d/m/Y H:i') : 'N/A' }}
                                                        </p>
                                                    </div>
                                                    
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 mt-2">
                                                        @if($registro->cantidad_ml)
                                                        <div class="flex items-center text-sm">
                                                            <i class="fas fa-tint text-blue-300 mr-2"></i>
                                                            <span class="text-gray-600">{{ $registro->cantidad_ml }} ml</span>
                                                        </div>
                                                        @endif
                                                        
                                                        @if($registro->metodo)
                                                        <div class="flex items-center text-sm">
                                                            <i class="fas fa-cog text-gray-400 mr-2"></i>
                                                            <span class="text-gray-600">{{ $registro->metodo }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    
                                                    @if($registro->observaciones)
                                                    <div class="mt-2 flex items-start">
                                                        <i class="fas fa-sticky-note text-yellow-500 mt-1 mr-2"></i>
                                                        <p class="text-sm text-gray-600">{{ $registro->observaciones }}</p>
                                                    </div>
                                                    @endif
                                                    
                                                    <p class="text-xs text-gray-500 mt-3">
                                                        Registrado por: {{ $registro->user->name }}
                                                    </p>
                                                </div>
                                                <div class="flex space-x-2 self-end sm:self-center">
                                                    <a href="{{ route('plantas.tareas.registros.show', [$planta->id, $tarea->id, $registro->id]) }}" class="text-blue-600 hover:text-blue-900 p-1 rounded transition-colors" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('plantas.tareas.registros.destroy', [$planta->id, $tarea->id, $registro->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded transition-colors" title="Eliminar registro" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Columna 2: Panel lateral con información adicional -->
                <div class="space-y-6">
                    <!-- Panel de Notificaciones -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h4 class="font-bold text-lg mb-4 text-gray-800 flex items-center">
                                <i class="fas fa-bell text-yellow-600 mr-2"></i>
                                Notificaciones y Recordatorios
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Recordatorio de próxima tarea -->
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <div class="flex items-start">
                                        <i class="fas fa-calendar-check text-green-500 mt-1 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Próxima ejecución</p>
                                            <p class="text-xs text-gray-600">
                                                {{ $tarea->proxima_fecha ? $tarea->proxima_fecha->format('d/m/Y') : 'N/A' }}
                                                @if($tarea->proxima_fecha)
                                                    @if($tarea->proxima_fecha->isToday())
                                                        <span class="ml-2 text-green-600 font-semibold">(Hoy)</span>
                                                    @elseif($tarea->proxima_fecha->isPast())
                                                        <span class="ml-2 text-red-600 font-semibold">(Atrasado)</span>
                                                    @else
                                                        <span class="ml-2 text-gray-600">
                                                            (en {{ $tarea->proxima_fecha->diffForHumans() }})
                                                        </span>
                                                    @endif
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Estadísticas de riego (solo para tareas de riego) -->
                                @if($tarea->tipo === 'riego' && !$tarea->registrosRiego->isEmpty())
                                    <div class="bg-gray-50 p-3 rounded-md">
                                        <div class="flex items-start">
                                            <i class="fas fa-chart-bar text-blue-500 mt-1 mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Estadísticas de Riego</p>
                                                <p class="text-xs text-gray-600">
                                                    Total de riegos: {{ $tarea->registrosRiego->count() }}<br>
                                                    @if($tarea->registrosRiego->avg('cantidad_ml'))
                                                        Promedio: {{ number_format($tarea->registrosRiego->avg('cantidad_ml'), 0) }} ml por riego
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Último riego -->
                                    <div class="bg-gray-50 p-3 rounded-md">
                                        <div class="flex items-start">
                                            <i class="fas fa-clock text-purple-500 mt-1 mr-3"></i>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">Último riego</p>
                                                <p class="text-xs text-gray-600">
                                                    @php
                                                        $ultimoRiego = $tarea->registrosRiego->sortByDesc('fecha_hora')->first();
                                                    @endphp
                                                    @if($ultimoRiego && $ultimoRiego->fecha_hora)
                                                        {{ $ultimoRiego->fecha_hora->format('d/m/Y H:i') }}
                                                        ({{ $ultimoRiego->fecha_hora->diffForHumans() }})
                                                    @else
                                                        No registrado
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Estado de la planta -->
                                <div class="bg-gray-50 p-3 rounded-md">
                                    <div class="flex items-start">
                                        <i class="fas fa-leaf text-green-500 mt-1 mr-3"></i>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Estado de la Planta</p>
                                            <p class="text-xs text-gray-600">
                                                {{ $planta->nombre }} - {{ $planta->especie }}<br>
                                                Ubicación: {{ $planta->ubicacion }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botón para ver actividades de esta planta -->
                    <a href="{{ route('plantas.actividades', $planta->id) }}" 
   class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
    <i class="fas fa-history mr-1"></i> Ver Historial de Actividades
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .scrollbar-thin {
            scrollbar-width: thin;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 3px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background-color: #a0aec0;
        }
    </style>
    @endpush
</x-app-layout>