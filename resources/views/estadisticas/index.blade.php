<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Estadísticas del Sistema
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('estadisticas.exportar') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-download mr-2"></i>Exportar Datos
                </a>
                <button onclick="location.reload()" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Actualizar
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tarjetas de resumen principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-leaf text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Plantas Totales</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_plantas'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600">
                            <i class="fas fa-tasks text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Tareas Totales</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['total_tareas'] }}</p>
                            <p class="text-xs text-green-600 mt-1">
                                {{ $estadisticas['tareas_pendientes'] }} pendientes
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                            <i class="fas fa-check-circle text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Tareas Completadas</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $estadisticas['tareas_completadas'] }}</p>
                            <p class="text-xs text-yellow-600 mt-1">
                                {{ $estadisticas['actividades_hoy'] }} hoy
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                            <i class="fas fa-tint text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Agua Utilizada</p>
                            <p class="text-3xl font-bold text-gray-900">
                                {{ number_format($estadisticas['total_agua'] / 1000, 1) }} L
                            </p>
                            <p class="text-xs text-purple-600 mt-1">
                                {{ number_format($estadisticas['agua_este_mes'] / 1000, 1) }}L este mes
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resumen semanal y Próxima tarea -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Actividad de la Semana</h3>
                    <div class="grid grid-cols-7 gap-2">
                        @foreach($estadisticas['resumen_semanal'] as $dia)
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">{{ $dia['dia'] }}</p>
                                <div class="bg-blue-50 rounded-lg p-2">
                                    <p class="text-lg font-bold {{ $dia['actividades'] > 0 ? 'text-blue-600' : 'text-gray-400' }}">
                                        {{ $dia['actividades'] }}
                                    </p>
                                </div>
                                <p class="text-xs text-gray-400 mt-1">{{ $dia['fecha'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Próxima Tarea</h3>
                    @if($estadisticas['proxima_tarea'])
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <p class="font-medium text-gray-900">{{ $estadisticas['proxima_tarea']->planta->nombre }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-tasks mr-1"></i> {{ ucfirst($estadisticas['proxima_tarea']->tipo) }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fas fa-calendar mr-1"></i> {{ $estadisticas['proxima_tarea']->proxima_fecha->format('d/m/Y') }}
                            </p>
                            <p class="text-xs text-yellow-600 mt-2">
                                En {{ $estadisticas['proxima_tarea']->proxima_fecha->diffInDays(now()) }} días
                            </p>
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay tareas pendientes</p>
                    @endif
                </div>
            </div>

            <!-- Estadísticas por tipo de actividad -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Actividades por Tipo</h3>
                    <div class="space-y-3">
                        @forelse($estadisticas['tipos_actividades'] as $tipo => $cantidad)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 rounded-full mr-2 
                                        {{ $tipo == 'riego' ? 'bg-blue-500' : '' }}
                                        {{ $tipo == 'fertilizacion' ? 'bg-green-500' : '' }}
                                        {{ $tipo == 'poda' ? 'bg-yellow-500' : '' }}
                                        {{ $tipo == 'trasplante' ? 'bg-purple-500' : '' }}
                                        {{ $tipo == 'otro' ? 'bg-gray-500' : '' }}">
                                    </div>
                                    <span class="text-sm text-gray-700">{{ ucfirst($tipo) }}</span>
                                </div>
                                <span class="text-lg font-semibold text-gray-900">{{ $cantidad }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">No hay actividades registradas</p>
                        @endforelse
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Actividades Recientes</h3>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse($estadisticas['actividad_reciente'] as $actividad)
                            <div class="border-l-2 border-blue-500 pl-3 py-1">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $actividad->planta->nombre }}
                                </p>
                                <p class="text-xs text-gray-600">
                                    {{ ucfirst($actividad->tipo) }} - {{ $actividad->created_at->diffForHumans() }}
                                </p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center">No hay actividades recientes</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Estadísticas mensuales -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Tendencia Mensual</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-2 text-sm text-gray-600">Mes</th>
                                <th class="text-center py-2 text-sm text-gray-600">Actividades</th>
                                <th class="text-center py-2 text-sm text-gray-600">Agua (L)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estadisticas['estadisticas_mensuales'] as $mes)
                                <tr class="border-b">
                                    <td class="py-2 text-sm text-gray-700">{{ $mes['mes'] }}</td>
                                    <td class="text-center py-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ $mes['actividades'] }}
                                        </span>
                                    </td>
                                    <td class="text-center py-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            {{ $mes['agua_litros'] }}L
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Estadísticas por planta -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Estadísticas por Planta</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Planta</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tareas</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actividades</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Agua Total</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Última Actividad</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($estadisticas['estadisticas_plantas'] as $planta)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $planta['nombre'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $planta['especie'] }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $planta['tareas_activas'] }}/{{ $planta['total_tareas'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-900">{{ $planta['total_actividades'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-900">{{ number_format($planta['agua_total'] / 1000, 2) }} L</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm text-gray-500">
                                            {{ $planta['ultima_actividad'] ? \Carbon\Carbon::parse($planta['ultima_actividad'])->diffForHumans() : 'Nunca' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No hay plantas registradas
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para actualización automática -->
    <script>
        // Actualizar estadísticas cada 5 minutos
        setInterval(function() {
            fetch('{{ route("estadisticas.json") }}')
                .then(response => response.json())
                .then(data => {
                    console.log('Estadísticas actualizadas:', data);
                });
        }, 300000);
    </script>
</x-app-layout>