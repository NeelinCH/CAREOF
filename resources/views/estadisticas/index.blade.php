<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Estadísticas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tarjetas de resumen -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <i class="fas fa-leaf text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Plantas Totales</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_plantas'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_tareas'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['tareas_completadas'] }}</p>
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
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($estadisticas['total_agua'] / 1000, 1) }} L</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próxima tarea -->
            @if($estadisticas['proxima_tarea'])
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Próxima Tarea Pendiente</h3>
                <div class="bg-blue-50 p-4 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="font-medium">{{ $estadisticas['proxima_tarea']->planta->nombre }}</p>
                            <p class="text-sm text-gray-600 capitalize">{{ $estadisticas['proxima_tarea']->tipo }}</p>
                            <p class="text-sm text-blue-600">
                                {{ $estadisticas['proxima_tarea']->proxima_fecha->format('d/m/Y') }}
                                ({{ $estadisticas['proxima_tarea']->proxima_fecha->diffForHumans() }})
                            </p>
                        </div>
                        <a href="{{ route('plantas.tareas.show', [$estadisticas['proxima_tarea']->planta->id, $estadisticas['proxima_tarea']->id]) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Ver Tarea
                        </a>
                    </div>
                </div>
            </div>
            @endif

            <!-- Actividad Reciente -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <h3 class="text-lg font-semibold mb-4">Actividad Reciente</h3>
                <div class="space-y-3">
                    @foreach($estadisticas['actividad_reciente'] as $actividad)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center bg-green-100 text-green-600">
                                <i class="fas fa-history"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium">{{ $actividad->descripcion }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $actividad->created_at->diffForHumans() }} • {{ $actividad->planta->nombre }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Estadísticas por Planta -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Estadísticas por Planta</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Planta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tareas Totales</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tareas Activas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actividades</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($estadisticas['estadisticas_plantas'] as $estadistica)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $estadistica['nombre'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $estadistica['total_tareas'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $estadistica['tareas_activas'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $estadistica['total_actividades'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>