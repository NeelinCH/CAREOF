<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de la Tarea') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Header con navegación -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <a href="{{ route('plantas.tareas.index', $planta->id) }}" 
                               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a tareas
                            </a>
                            <h3 class="text-lg font-semibold mt-2 text-gray-800">
                                Tarea de {{ ucfirst($tarea->tipo) }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Para la planta: {{ $planta->nombre }}
                            </p>
                        </div>

                        <div class="flex space-x-2">
                            <a href="{{ route('plantas.tareas.edit', [$planta->id, $tarea->id]) }}" 
                               class="inline-flex items-center px-3 py-1 bg-yellow-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-yellow-600 transition">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <form action="{{ route('plantas.tareas.destroy', [$planta->id, $tarea->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-3 py-1 bg-red-500 rounded-md font-semibold text-xs text-white uppercase hover:bg-red-600 transition" 
                                        onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Estado de la tarea -->
                    <div class="mb-6">
                        <div class="flex items-center justify-between p-4 rounded-lg {{ $tarea->activa ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                            <div class="flex items-center">
                                <div class="p-2 rounded-full {{ $tarea->activa ? 'bg-green-100' : 'bg-gray-100' }}">
                                    <i class="fas {{ $tarea->activa ? 'fa-play-circle text-green-600' : 'fa-pause-circle text-gray-500' }} text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="font-medium {{ $tarea->activa ? 'text-green-900' : 'text-gray-700' }}">
                                        Estado: {{ $tarea->activa ? 'Activa' : 'Inactiva' }}
                                    </h4>
                                    <p class="text-sm {{ $tarea->activa ? 'text-green-600' : 'text-gray-500' }}">
                                        {{ $tarea->activa ? 'La tarea está programada y activa' : 'La tarea está pausada' }}
                                    </p>
                                </div>
                            </div>
                            
                            @if($tarea->activa)
                                @if($tarea->proxima_fecha && $tarea->proxima_fecha->isToday())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-exclamation-triangle mr-1"></i> Programada para hoy
                                    </span>
                                @elseif($tarea->proxima_fecha && $tarea->proxima_fecha->isPast())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="fas fa-clock mr-1"></i> Atrasada
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>

                    <!-- Información principal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Información básica -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-bold text-lg mb-4 flex items-center text-blue-700">
                                <i class="fas fa-info-circle mr-2"></i> Información de la Tarea
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Tipo:</span>
                                    <span class="font-medium capitalize text-gray-900">{{ $tarea->tipo }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Frecuencia:</span>
                                    <span class="text-gray-900">Cada {{ $tarea->frecuencia_dias }} días</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Próxima fecha:</span>
                                    <span class="text-gray-900">
                                        {{ $tarea->proxima_fecha ? $tarea->proxima_fecha->format('d/m/Y') : 'No programada' }}
                                        @if($tarea->proxima_fecha && $tarea->proxima_fecha->isToday())
                                            <span class="ml-1 text-orange-600">(Hoy)</span>
                                        @elseif($tarea->proxima_fecha && $tarea->proxima_fecha->isPast())
                                            <span class="ml-1 text-red-600">(Atrasado)</span>
                                        @endif
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Última ejecución:</span>
                                    <span class="text-gray-900">
                                        {{ $tarea->ultima_ejecucion ? $tarea->ultima_ejecucion->format('d/m/Y H:i') : 'Nunca' }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Creada:</span>
                                    <span class="text-gray-900">{{ $tarea->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la planta -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-bold text-lg mb-4 flex items-center text-green-700">
                                <i class="fas fa-leaf mr-2"></i> Información de la Planta
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Nombre:</span>
                                    <span class="text-gray-900">{{ $planta->nombre }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Especie:</span>
                                    <span class="text-gray-900">{{ $planta->especie }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Ubicación:</span>
                                    <span class="text-gray-900">{{ $planta->ubicacion }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    @if($tarea->descripcion)
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-6">
                        <h4 class="font-bold text-lg mb-4 flex items-center text-blue-600">
                            <i class="fas fa-file-alt mr-2"></i> Descripción
                        </h4>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-gray-800 leading-relaxed">{{ $tarea->descripcion }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Acciones de completar tarea -->
                    @if($tarea->activa)
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200 shadow-sm mb-6">
                        <h4 class="font-bold text-lg mb-4 flex items-center text-blue-700">
                            <i class="fas fa-check-circle mr-2"></i> Completar Tarea
                        </h4>
                        
                        @if($tarea->tipo === 'riego')
                            <!-- Formulario específico para riego -->
                            <form action="{{ route('plantas.tareas.completar.store', [$planta->id, $tarea->id]) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-1">Cantidad (ml)</label>
                                        <input type="number" name="cantidad_ml" value="500" min="1" 
                                               class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-blue-700 mb-1">Método</label>
                                        <select name="metodo" class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                            <option value="Manual">Manual</option>
                                            <option value="Arduino">Arduino</option>
                                            <option value="Goteo">Sistema de goteo</option>
                                        </select>
                                    </div>
                                    <div class="md:col-span-1 flex items-end">
                                        <button type="submit" 
                                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition duration-200 flex items-center justify-center">
                                            <i class="fas fa-tint mr-2"></i> Completar Riego
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1">Observaciones (opcional)</label>
                                    <textarea name="observaciones" rows="2" 
                                              class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                              placeholder="Notas adicionales sobre el riego..."></textarea>
                                </div>
                            </form>
                        @else
                            <!-- Botón simple para otras tareas -->
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-700 mb-2">¿Has completado esta tarea de {{ $tarea->tipo }}?</p>
                                    <p class="text-sm text-blue-600">Se registrará la fecha y hora actual y se calculará la próxima fecha.</p>
                                </div>
                                <form action="{{ route('plantas.tareas.completar.store', [$planta->id, $tarea->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded transition duration-200 flex items-center">
                                        <i class="fas fa-check mr-2"></i> Marcar como Completada
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Control de riego automático (solo para tareas de riego) -->
                    @if($tarea->tipo === 'riego' && $tarea->activa)
                    <div class="mb-6">
                        @livewire('riego-control', [
                            'plantaId' => $planta->id,
                            'tareaId' => $tarea->id
                        ])
                    </div>
                    @endif

                    <!-- Registros recientes -->
                    @if($tarea->tipo === 'riego')
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-lg flex items-center text-gray-700">
                                <i class="fas fa-history mr-2"></i> Registros Recientes de Riego
                            </h4>
                            <a href="{{ route('plantas.tareas.registros.index', [$planta->id, $tarea->id]) }}" 
                               class="inline-flex items-center px-3 py-1 bg-gray-600 text-white rounded-md text-xs hover:bg-gray-700 transition">
                                <i class="fas fa-list mr-1"></i> Ver todos los registros
                            </a>
                        </div>
                        
                        @if($registrosRecientes->count() > 0)
                            <div class="space-y-3">
                                @foreach($registrosRecientes as $registro)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                                        <div class="flex items-center">
                                            <div class="p-2 bg-blue-100 rounded-full">
                                                <i class="fas fa-tint text-blue-600"></i>
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium text-gray-900">{{ $registro->cantidad_ml ?? '0' }} ml</p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $registro->fecha_hora->format('d/m/Y H:i') }} - {{ $registro->metodo ?? 'Manual' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-600">{{ $registro->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $registro->fecha_hora->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center p-6 text-gray-500">
                                <i class="fas fa-droplet text-3xl mb-2"></i>
                                <p>No hay registros de riego para esta tarea.</p>
                                <p class="text-sm">Los registros aparecerán aquí cuando completes la tarea de riego.</p>
                            </div>
                        @endif
                    </div>
                    @endif

                    <!-- Botones de navegación -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('plantas.tareas.index', $planta->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md text-sm uppercase hover:bg-gray-700 transition">
                            <i class="fas fa-tasks mr-2"></i> Ver Tareas
                        </a>
                        <a href="{{ route('plantas.show', $planta->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm uppercase hover:bg-green-700 transition">
                            <i class="fas fa-leaf mr-2"></i> Ver Planta
                        </a>
                        <a href="{{ route('plantas.actividades', $planta->id) }}" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md text-sm uppercase hover:bg-blue-700 transition">
                            <i class="fas fa-history mr-2"></i> Ver Historial
                        </a>
                        <a href="{{ route('plantas.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm uppercase hover:bg-indigo-700 transition">
                            <i class="fas fa-seedling mr-2"></i> Todas las Plantas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .border-b:last-child {
            border-bottom: none !important;
        }
    </style>
    @endpush
</x-app-layout>