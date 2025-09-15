<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Actividades de') }}: {{ ucfirst($tipo) }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Breadcrumb -->
            <div class="mb-6">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('actividades.index') }}" class="text-sm text-gray-700 hover:text-blue-600">
                                <i class="fas fa-history mr-1"></i> Todas las Actividades
                            </a>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                <span class="text-sm text-gray-500 capitalize">{{ $tipo }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>
            </div>

            <!-- Estadísticas del tipo específico -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full 
                            @switch($tipo)
                                @case('riego') bg-blue-100 text-blue-600 @break
                                @case('poda') bg-green-100 text-green-600 @break
                                @case('fertilizacion') bg-yellow-100 text-yellow-600 @break
                                @case('trasplante') bg-purple-100 text-purple-600 @break
                                @default bg-gray-100 text-gray-600
                            @endswitch">
                            <i class="fas 
                                @switch($tipo)
                                    @case('riego') fa-tint @break
                                    @case('poda') fa-cut @break
                                    @case('fertilizacion') fa-flask @break
                                    @case('trasplante') fa-seedling @break
                                    @default fa-tasks
                                @endswitch text-2xl">
                            </i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total {{ ucfirst($tipo) }}s</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $actividades->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-calendar-day text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Hoy</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $actividades->filter(function($actividad) { return $actividad->created_at->isToday(); })->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-calendar-week text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Esta Semana</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $actividades->filter(function($actividad) { return $actividad->created_at->isCurrentWeek(); })->count() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-calendar-alt text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Este Mes</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $actividades->filter(function($actividad) { return $actividad->created_at->isCurrentMonth(); })->count() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros de navegación -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Filtrar por Tipo</h3>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('actividades.index') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium bg-gray-100 text-gray-800 hover:bg-gray-200">
                       <i class="fas fa-all text-xs mr-1"></i> Todos
                    </a>
                    <a href="{{ route('actividades.tipo', 'riego') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium {{ $tipo === 'riego' ? 'bg-blue-500 text-white' : 'bg-blue-100 text-blue-800 hover:bg-blue-200' }}">
                       <i class="fas fa-tint text-xs mr-1"></i> Riegos
                    </a>
                    <a href="{{ route('actividades.tipo', 'poda') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium {{ $tipo === 'poda' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                       <i class="fas fa-cut text-xs mr-1"></i> Podas
                    </a>
                    <a href="{{ route('actividades.tipo', 'fertilizacion') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium {{ $tipo === 'fertilizacion' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' }}">
                       <i class="fas fa-flask text-xs mr-1"></i> Fertilizaciones
                    </a>
                    <a href="{{ route('actividades.tipo', 'trasplante') }}" 
                       class="px-4 py-2 rounded-full text-sm font-medium {{ $tipo === 'trasplante' ? 'bg-purple-500 text-white' : 'bg-purple-100 text-purple-800 hover:bg-purple-200' }}">
                       <i class="fas fa-seedling text-xs mr-1"></i> Trasplantes
                    </a>
                </div>
            </div>

            <!-- Lista de Actividades -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 
                    @switch($tipo)
                        @case('riego') bg-blue-50 @break
                        @case('poda') bg-green-50 @break
                        @case('fertilizacion') bg-yellow-50 @break
                        @case('trasplante') bg-purple-50 @break
                        @default bg-gray-50
                    @endswitch">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            Actividades de {{ ucfirst($tipo) }}
                        </h3>
                        <span class="text-sm 
                            @switch($tipo)
                                @case('riego') text-blue-600 @break
                                @case('poda') text-green-600 @break
                                @case('fertilizacion') text-yellow-600 @break
                                @case('trasplante') text-purple-600 @break
                                @default text-gray-600
                            @endswitch">
                            {{ $actividades->total() }} registros
                        </span>
                    </div>
                </div>

                @if($actividades->isEmpty())
                    <div class="p-6 text-center">
                        <i class="fas 
                            @switch($tipo)
                                @case('riego') fa-tint @break
                                @case('poda') fa-cut @break
                                @case('fertilizacion') fa-flask @break
                                @case('trasplante') fa-seedling @break
                                @default fa-tasks
                            @endswitch text-4xl text-gray-300 mb-4">
                        </i>
                        <p class="text-gray-500">No hay actividades de {{ $tipo }} registradas todavía.</p>
                        <p class="text-sm text-gray-400 mt-2">Las actividades se registrarán cuando completes tareas de este tipo.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200">
                        @foreach($actividades as $actividad)
                            <div class="p-6 hover:bg-gray-50 transition duration-150">
                                <div class="flex items-start">
                                    <!-- Icono según el tipo -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center 
                                            @switch($tipo)
                                                @case('riego') bg-blue-100 text-blue-600 @break
                                                @case('poda') bg-green-100 text-green-600 @break
                                                @case('fertilizacion') bg-yellow-100 text-yellow-600 @break
                                                @case('trasplante') bg-purple-100 text-purple-600 @break
                                                @default bg-gray-100 text-gray-600
                                            @endswitch">
                                            <i class="fas 
                                                @switch($tipo)
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
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <i class="fas fa-leaf mr-1"></i>{{ $actividad->planta->nombre }}
                                                    @if($actividad->planta->especie)
                                                        • <i class="fas fa-tag mr-1"></i>{{ $actividad->planta->especie }}
                                                    @endif
                                                    @if($actividad->planta->ubicacion)
                                                        • <i class="fas fa-map-marker-alt mr-1"></i>{{ $actividad->planta->ubicacion }}
                                                    @endif
                                                </p>
                                            </div>
                                            <span class="text-sm text-gray-400">
                                                {{ $actividad->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <!-- Detalles específicos del tipo -->
                                        @if($actividad->detalles && is_array($actividad->detalles))
                                            <div class="mt-2 
                                                @switch($tipo)
                                                    @case('riego') bg-blue-50 @break
                                                    @case('poda') bg-green-50 @break
                                                    @case('fertilizacion') bg-yellow-50 @break
                                                    @case('trasplante') bg-purple-50 @break
                                                    @default bg-gray-50
                                                @endswitch p-3 rounded-lg">
                                                <p class="text-xs text-gray-600">
                                                    @if($tipo === 'riego')
                                                        <i class="fas fa-tint mr-1 text-blue-500"></i>
                                                        {{ $actividad->detalles['cantidad_ml'] ?? '0' }}ml 
                                                        @if(isset($actividad->detalles['metodo']))
                                                            • Método: {{ $actividad->detalles['metodo'] }}
                                                        @endif
                                                        @if(isset($actividad->detalles['observaciones']) && $actividad->detalles['observaciones'])
                                                            <br><i class="fas fa-sticky-note mr-1 text-gray-500"></i>
                                                            {{ $actividad->detalles['observaciones'] }}
                                                        @endif
                                                    @else
                                                        <i class="fas fa-info-circle mr-1 text-gray-500"></i>
                                                        @if(isset($actividad->detalles['descripcion']))
                                                            {{ $actividad->detalles['descripcion'] }}
                                                        @elseif(isset($actividad->detalles['tipo_tarea']))
                                                            Tarea de {{ $actividad->detalles['tipo_tarea'] }} completada
                                                            @if(isset($actividad->detalles['frecuencia_dias']))
                                                                • Frecuencia: cada {{ $actividad->detalles['frecuencia_dias'] }} días
                                                            @endif
                                                        @endif
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