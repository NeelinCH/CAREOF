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

            <!-- Resumen semanal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Actividad de la Semana</h3>
                    <div class="gri