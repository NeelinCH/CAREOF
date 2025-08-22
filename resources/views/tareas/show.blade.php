<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de Tarea') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <a href="{{ route('plantas.tareas.index', $planta->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a tareas
                            </a>
                            <h3 class="text-lg font-medium mt-2">Detalles de la Tarea</h3>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('plantas.tareas.edit', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <form action="{{ route('plantas.tareas.destroy', [$planta->id, $tarea->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Información de la Tarea -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="font-bold text-lg mb-4">Información de la Tarea</h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Tipo de tarea</p>
                                        <p class="mt-1 text-sm text-gray-900 capitalize">
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
                                            {{ $tarea->tipo }}
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Descripción</p>
                                        <p class="mt-1 text-sm text-gray-900">{{ $tarea->descripcion ?? 'Sin descripción' }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Frecuencia</p>
                                        <p class="mt-1 text-sm text-gray-900">Cada {{ $tarea->frecuencia_dias }} días</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Próxima fecha</p>
                                        <p class="mt-1 text-sm text-gray-900">
                                            {{ $tarea->proxima_fecha ? $tarea->proxima_fecha->format('d/m/Y') : 'N/A' }}
                                            @if($tarea->proxima_fecha && $tarea->proxima_fecha->isToday())
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hoy</span>
                                            @elseif($tarea->proxima_fecha && $tarea->proxima_fecha->isPast())
                                                <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Atrasado</span>
                                            @endif
                                        </p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Estado</p>
                                        <p class="mt-1 text-sm text-gray-900">
                                            @if($tarea->activa)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactiva</span>
                                            @endif
                                        </p>
                                    </div>

                                    <!-- Botón para ver actividades de esta planta -->
                                    <div class="mt-4">
                                        <a href="{{ route('plantas.actividades', $planta->id) }}" class="inline-flex items-center px-3 py-2 bg-purple-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-600 active:bg-purple-700 focus:outline-none focus:border-purple-700 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-history mr-1"></i> Ver Historial de Actividades
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Control de Riego Arduino (solo para tareas de riego) -->
                            @if($tarea->tipo === 'riego')
                                <div class="mt-6">
                                    @livewire('riego-control', ['planta' => $planta, 'tarea' => $tarea])
                                </div>
                            @endif
                        </div>

                        <!-- Registros de Riego y Notificaciones -->
                        <div class="md:col-span-1">
                            @if($tarea->tipo === 'riego')
                                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h4 class="font-bold text-lg">Registros de Riego</h4>
                                        <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            <i class="fas fa-plus mr-1"></i> Nuevo Registro
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
                                        <div class="space-y-3 max-h-96 overflow-y-auto">
                                            @foreach($tarea->registrosRiego->sortByDesc('fecha_hora') as $registro)
                                                <div class="bg-white p-3 rounded-md shadow-sm border border-gray-200">
                                                    <div class="flex justify-between items-start">
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-900">
                                                                <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                                                                {{ $registro->fecha_hora ? $registro->fecha_hora->format('d/m/Y H:i') : 'N/A' }}
                                                            </p>
                                                            @if($registro->cantidad_ml)
                                                                <p class="text-xs text-gray-600 mt-1">
                                                                    <i class="fas fa-tint text-blue-300 mr-1"></i>
                                                                    {{ $registro->cantidad_ml }} ml
                                                                </p>
                                                            @endif
                                                            @if($registro->metodo)
                                                                <p class="text-xs text-gray-600 mt-1">
                                                                    <i class="fas fa-cog text-gray-400 mr-1"></i>
                                                                    {{ $registro->metodo }}
                                                                </p>
                                                            @endif
                                                            @if($registro->observaciones)
                                                                <p class="text-xs text-gray-600 mt-2">
                                                                    <i class="fas fa-sticky-note text-yellow-500 mr-1"></i>
                                                                    {{ $registro->observaciones }}
                                                                </p>
                                                            @endif
                                                            <p class="text-xs text-gray-400 mt-2">
                                                                Registrado por: {{ $registro->user->name }}
                                                            </p>
                                                        </div>
                                                        <div class="flex space-x-2 ml-3">
                                                            <a href="{{ route('plantas.tareas.registros.show', [$planta->id, $tarea->id, $registro->id]) }}" class="text-blue-600 hover:text-blue-900 text-sm">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <form action="{{ route('plantas.tareas.registros.destroy', [$planta->id, $tarea->id, $registro->id]) }}" method="POST" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
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
                            @endif

                            <!-- Panel de Notificaciones -->
                            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                <h4 class="font-bold text-lg mb-4 flex items-center">
                                    <i class="fas fa-bell text-yellow-600 mr-2"></i>
                                    Notificaciones y Recordatorios
                                </h4>
                                
                                <div class="space-y-3">
                                    <!-- Recordatorio de próxima tarea -->
                                    <div class="bg-white p-3 rounded-md shadow-sm">
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
                                        <div class="bg-white p-3 rounded-md shadow-sm">
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
                                        <div class="bg-white p-3 rounded-md shadow-sm">
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
                                    <div class="bg-white p-3 rounded-md shadow-sm">
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .max-h-96 {
            max-height: 24rem;
        }
        .scrollbar-thin::-webkit-scrollbar {
            width: 4px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 2px;
        }
    </style>
    @endpush
</x-app-layout>