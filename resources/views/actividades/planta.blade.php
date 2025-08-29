<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Actividades de') }}: {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('plantas.index') }}" class="text-sm text-gray-700 hover:text-blue-600">
                                <i class="fas fa-home mr-1"></i> Plantas
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <a href="{{ route('plantas.show', $planta->id) }}" class="text-sm text-gray-700 hover:text-blue-600">
                                    {{ $planta->nombre }}
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm text-gray-500">Actividades</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Estadísticas de la Planta -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-history text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Actividades</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-tint text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Riegos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['riego'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-cut text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Podas</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['poda'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-flask text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Fertilizaciones</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['fertilizacion'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Planta -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Información de la Planta</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600"><strong>Nombre:</strong> {{ $planta->nombre }}</p>
                        <p class="text-sm text-gray-600"><strong>Especie:</strong> {{ $planta->especie }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600"><strong>Ubicación:</strong> {{ $planta->ubicacion }}</p>
                        <p class="text-sm text-gray-600"><strong>Fecha de adquisición:</strong> {{ $planta->fecha_adquisicion->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Lista de Actividades -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Actividades de {{ $planta->nombre }}</h3>
                        <span class="text-sm text-blue-600">{{ $actividades->total() }} registros</span>
                    </div>
                </div>

                @if($actividades->isEmpty())
                    <div class="p-6 text-center">
                        <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No hay actividades registradas para esta planta.</p>
                        <p class="text-sm text-gray-400 mt-2">Las actividades se registrarán automáticamente cuando realices acciones en esta planta.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($actividades as $actividad)
                            <div class="p-6 hover:bg-gray-50 transition duration-150">
                                <div class="flex items-start">
                                    <!-- Icono según el tipo -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                            @switch($actividad->tipo)
                                                @case('riego') bg-blue-100 text-blue-600 @break
                                                @case('poda') bg-green-100 text-green-600 @break
                                                @case('fertilizacion') bg-yellow-100 text-yellow-600 @break
                                                @case('trasplante') bg-purple-100 text-purple-600 @break
                                                @default bg-gray-100 text-gray-600
                                            @endswitch">
                                            <i class="fas 
                                                @switch($actividad->tipo)
                                                    @case('riego') fa-tint @break
                                                    @case('poda') fa-cut @break
                                                    @case('fertilizacion') fa-flask @break
                                                    @case('trasplante') fa-seedling @break
                                                    @default fa-tasks
                                                @endswitch">
                                            </i>
                                        </div>
                                    </div>

                                    <div class="ml-4 flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $actividad->user->name }}
                                                    <span class="text-sm font-normal text-gray-500 ml-2">
                                                        {{ $actividad->descripcion }}
                                                    </span>
                                                </p>
                                                <p class="text-xs text-gray-500 mt-1 capitalize">
                                                    <i class="fas fa-tag mr-1"></i>{{ $actividad->tipo }}
                                                </p>
                                            </div>
                                            <span class="text-sm text-gray-400">
                                                {{ $actividad->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Detalles específicos -->
                                        @if($actividad->detalles && is_array($actividad->detalles))
                                            <div class="mt-2 bg-gray-50 p-3 rounded-lg">
                                                <p class="text-xs text-gray-600">
                                                    @if($actividad->tipo === 'riego' && isset($actividad->detalles['cantidad_ml']))
                                                        <i class="fas fa-tint mr-1 text-blue-500"></i>
                                                        {{ $actividad->detalles['cantidad_ml'] }}ml 
                                                        @if(isset($actividad->detalles['metodo']))
                                                            • Método: {{ $actividad->detalles['metodo'] }}
                                                        @endif
                                                    @elseif(isset($actividad->detalles['descripcion']))
                                                        <i class="fas fa-info-circle mr-1 text-gray-500"></i>
                                                        {{ $actividad->detalles['descripcion'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endif

                                        <p class="text-xs text-gray-400 mt-2">
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $actividad->created_at->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Paginación -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $actividades->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>