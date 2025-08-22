<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de Registro de Riego') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header con navegación y acciones -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <a href="{{ route('plantas.tareas.registros.index', [$planta->id, $tarea->id]) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a registros
                            </a>
                            <h3 class="text-lg font-medium mt-2">Detalles del Registro de Riego</h3>
                            <p class="text-sm text-gray-600 mt-1">Tarea: {{ ucfirst($tarea->tipo) }} - {{ $planta->nombre }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('plantas.tareas.registros.edit', [$planta->id, $tarea->id, $registroRiego->id]) }}" class="inline-flex items-center px-3 py-1 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring ring-yellow-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-edit mr-1"></i> Editar
                            </a>
                            <form action="{{ route('plantas.tareas.registros.destroy', [$planta->id, $tarea->id, $registroRiego->id]) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 active:bg-red-700 focus:outline-none focus:border-red-700 focus:ring ring-red-300 disabled:opacity-25 transition ease-in-out duration-150" onclick="return confirm('¿Estás seguro de eliminar este registro?')">
                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Tarjetas de información rápida -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-3 rounded-full">
                                    <i class="fas fa-tint text-blue-600 text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-900">Cantidad de Agua</p>
                                    <p class="text-2xl font-bold text-blue-600">
                                        {{ $registroRiego->cantidad_ml ?? '0' }} ml
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-3 rounded-full">
                                    <i class="fas fa-clock text-green-600 text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-900">Duración</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        @if($registroRiego->metodo == 'Arduino USB')
                                            2 segundos
                                        @else
                                            {{ $registroRiego->duracion ?? 'N/A' }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-3 rounded-full">
                                    <i class="fas fa-user text-purple-600 text-lg"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-purple-900">Registrado por</p>
                                    <p class="text-lg font-bold text-purple-600">
                                        {{ $registroRiego->user->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información detallada -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Información del Riego -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-bold text-lg mb-4 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Información del Riego
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Fecha y Hora:</span>
                                    <span class="text-sm text-gray-900">
                                        {{ $registroRiego->fecha_hora->format('d/m/Y H:i') }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Hace:</span>
                                    <span class="text-sm text-blue-600 font-medium">
                                        {{ $registroRiego->fecha_hora->diffForHumans() }}
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Método:</span>
                                    <span class="text-sm text-gray-900">
                                        @if($registroRiego->metodo)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <i class="fas fa-cog mr-1"></i> {{ $registroRiego->metodo }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">N/A</span>
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Tipo de Riego:</span>
                                    <span class="text-sm text-gray-900">
                                        @if($registroRiego->metodo == 'Arduino USB')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-microchip mr-1"></i> Automático
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-hand-paper mr-1"></i> Manual
                                            </span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la Tarea y Planta -->
                        <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm">
                            <h4 class="font-bold text-lg mb-4 flex items-center">
                                <i class="fas fa-leaf text-green-500 mr-2"></i>
                                Contexto
                            </h4>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Planta:</span>
                                    <span class="text-sm text-gray-900">{{ $planta->nombre }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Especie:</span>
                                    <span class="text-sm text-gray-900">{{ $planta->especie }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Ubicación:</span>
                                    <span class="text-sm text-gray-900">{{ $planta->ubicacion }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Tarea:</span>
                                    <span class="text-sm text-gray-900 capitalize">{{ $tarea->tipo }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm font-medium text-gray-600">Frecuencia:</span>
                                    <span class="text-sm text-gray-900">Cada {{ $tarea->frecuencia_dias }} días</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm font-medium text-gray-600">Próximo riego:</span>
                                    <span class="text-sm text-gray-900">
                                        {{ $tarea->proxima_fecha->format('d/m/Y') }}
                                        @if($tarea->proxima_fecha->isToday())
                                            <span class="ml-1 text-green-600">(Hoy)</span>
                                        @elseif($tarea->proxima_fecha->isPast())
                                            <span class="ml-1 text-red-600">(Atrasado)</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm mb-6">
                        <h4 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>
                            Observaciones
                        </h4>
                        
                        <div class="bg-gray-50 p-4 rounded-lg">
                            @if($registroRiego->observaciones)
                                <p class="text-gray-800 leading-relaxed">{{ $registroRiego->observaciones }}</p>
                            @else
                                <p class="text-gray-400 italic">No hay observaciones registradas para este riego.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Información del Sistema -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h4 class="font-bold text-lg mb-4 flex items-center">
                            <i class="fas fa-database text-gray-500 mr-2"></i>
                            Información del Sistema
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600"><span class="font-medium">ID del Registro:</span> {{ $registroRiego->id }}</p>
                                <p class="text-gray-600"><span class="font-medium">Creado:</span> {{ $registroRiego->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-gray-600"><span class="font-medium">Actualizado:</span> {{ $registroRiego->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600"><span class="font-medium">Usuario ID:</span> {{ $registroRiego->user->id }}</p>
                                <p class="text-gray-600"><span class="font-medium">Email:</span> {{ $registroRiego->user->email }}</p>
                                <p class="text-gray-600"><span class="font-medium">Tarea ID:</span> {{ $tarea->id }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Acciones adicionales -->
                    <div class="mt-6 flex flex-wrap gap-3">
                        <a href="{{ route('plantas.tareas.show', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-800 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-tasks mr-2"></i> Ver Tarea
                        </a>
                        <a href="{{ route('plantas.show', $planta->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-leaf mr-2"></i> Ver Planta
                        </a>
                        <a href="{{ route('plantas.actividades', $planta->id) }}" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 active:bg-purple-800 focus:outline-none focus:border-purple-800 focus:ring ring-purple-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-history mr-2"></i> Ver Historial
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