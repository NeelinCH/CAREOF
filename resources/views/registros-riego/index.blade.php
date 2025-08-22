<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Registros de Riego') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <!-- Header con navegación y acciones -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <a href="{{ route('plantas.tareas.show', [$planta->id, $tarea->id]) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a la tarea
                            </a>
                            <h3 class="text-lg font-medium mt-2">Historial de Riegos</h3>
                            <p class="text-sm text-gray-600 mt-1">Tarea: {{ ucfirst($tarea->tipo) }} - Cada {{ $tarea->frecuencia_dias }} días</p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                <i class="fas fa-plus mr-2"></i> Nuevo Registro
                            </a>
                            <!-- Botón para activar riego con Arduino -->
                            @if($tarea->tipo === 'riego')
                                <a href="{{ route('plantas.tareas.show', [$planta->id, $tarea->id]) }}#riego-control" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-tint mr-2"></i> Riego Arduino
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Estadísticas rápidas -->
                    @if(!$registros->isEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-full">
                                    <i class="fas fa-tint text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-900">Total Riegos</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $registros->count() }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <i class="fas fa-chart-line text-green-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-900">Promedio</p>
                                    <p class="text-2xl font-bold text-green-600">
                                        {{ number_format($registros->avg('cantidad_ml'), 0) }} ml
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-full">
                                    <i class="fas fa-clock text-purple-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-purple-900">Último Riego</p>
                                    <p class="text-sm font-bold text-purple-600">
                                        @if($registros->first()->fecha_hora)
                                            {{ $registros->first()->fecha_hora->diffForHumans() }}
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                            <div class="flex items-center">
                                <div class="bg-orange-100 p-2 rounded-full">
                                    <i class="fas fa-user text-orange-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-orange-900">Registrado por</p>
                                    <p class="text-sm font-bold text-orange-600">
                                        {{ $registros->first()->user->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($registros->isEmpty())
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No hay registros de riego para esta tarea. <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" class="font-medium underline text-blue-700 hover:text-blue-600">Añade un registro</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Filtros y búsqueda -->
                        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">Mostrando {{ $registros->count() }} registros</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button onclick="exportToCSV()" class="inline-flex items-center px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-xs text-gray-700 hover:bg-gray-200 transition duration-150">
                                    <i class="fas fa-download mr-1"></i> Exportar CSV
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-calendar-alt mr-2"></i> Fecha y Hora
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-weight mr-2"></i> Cantidad
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-cog mr-2"></i> Método
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2"></i> Registrado por
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-sticky-note mr-2"></i> Observaciones
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <div class="flex items-center">
                                                <i class="fas fa-cogs mr-2"></i> Acciones
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($registros as $registro)
                                        <tr class="hover:bg-gray-50 transition duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $registro->fecha_hora->format('d/m/Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $registro->fecha_hora->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-blue-600">
                                                    {{ $registro->fecha_hora->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $registro->cantidad_ml ?? '0' }} ml
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if($registro->metodo)
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            <i class="fas fa-cog mr-1"></i> {{ $registro->metodo }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">N/A</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $registro->user->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $registro->user->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900 max-w-xs truncate">
                                                    @if($registro->observaciones)
                                                        {{ Str::limit($registro->observaciones, 50) }}
                                                    @else
                                                        <span class="text-gray-400">Sin observaciones</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <a href="{{ route('plantas.tareas.registros.show', [$planta->id, $tarea->id, $registro->id]) }}" 
                                                       class="text-blue-600 hover:text-blue-900 p-1 rounded-full hover:bg-blue-100 transition duration-150"
                                                       title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('plantas.tareas.registros.edit', [$planta->id, $tarea->id, $registro->id]) }}" 
                                                       class="text-yellow-600 hover:text-yellow-900 p-1 rounded-full hover:bg-yellow-100 transition duration-150"
                                                       title="Editar registro">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('plantas.tareas.registros.destroy', [$planta->id, $tarea->id, $registro->id]) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="text-red-600 hover:text-red-900 p-1 rounded-full hover:bg-red-100 transition duration-150"
                                                                onclick="return confirm('¿Estás seguro de eliminar este registro?')"
                                                                title="Eliminar registro">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Resumen total -->
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-lg mb-3">Resumen Total</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <p class="text-sm text-gray-600">Total de agua utilizada:</p>
                                    <p class="text-lg font-bold text-blue-600">
                                        {{ number_format($registros->sum('cantidad_ml'), 0) }} ml
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Promedio por riego:</p>
                                    <p class="text-lg font-bold text-green-600">
                                        {{ number_format($registros->avg('cantidad_ml'), 0) }} ml
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Período cubierto:</p>
                                    <p class="text-lg font-bold text-purple-600">
                                        @if($registros->count() > 1)
                                            {{ $registros->last()->fecha_hora->diffInDays($registros->first()->fecha_hora) }} días
                                        @else
                                            1 día
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Frecuencia promedio:</p>
                                    <p class="text-lg font-bold text-orange-600">
                                        @if($registros->count() > 1)
                                            {{ number_format($registros->last()->fecha_hora->diffInDays($registros->first()->fecha_hora) / ($registros->count() - 1), 1) }} días
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function exportToCSV() {
        // Crear contenido CSV
        let csv = 'Fecha,Hora,Cantidad (ml),Método,Observaciones,Registrado por\n';
        
        @foreach($registros as $registro)
        csv += '{{ $registro->fecha_hora->format("Y-m-d") }},';
        csv += '{{ $registro->fecha_hora->format("H:i") }},';
        csv += '{{ $registro->cantidad_ml ?? "0" }},';
        csv += '"{{ $registro->metodo ?? "N/A" }}",';
        csv += '"{{ $registro->observaciones ? addslashes($registro->observaciones) : "Sin observaciones" }}",';
        csv += '"{{ $registro->user->name }}"';
        csv += '\n';
        @endforeach

        // Crear blob y descargar
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'riegos-{{ $planta->nombre }}-{{ now()->format("Y-m-d") }}.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    </script>
    @endpush

    @push('styles')
    <style>
    .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    </style>
    @endpush
</x-app-layout>