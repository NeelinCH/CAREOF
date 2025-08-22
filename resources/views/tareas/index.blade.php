<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tareas de') }}: {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <a href="{{ route('plantas.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a mis plantas
                            </a>
                            <h3 class="text-lg font-medium mt-2">Listado de Tareas</h3>
                        </div>
                        <a href="{{ route('plantas.tareas.create', $planta->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i> Nueva Tarea
                        </a>
                    </div>

                    @if($tareas->isEmpty())
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No hay tareas programadas para esta planta. <a href="{{ route('plantas.tareas.create', $planta->id) }}" class="font-medium underline text-blue-700 hover:text-blue-600">Añade una tarea</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Próxima Fecha</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($tareas as $tarea)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
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
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">{{ $tarea->descripcion ?? 'Sin descripción' }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $tarea->proxima_fecha->format('d/m/Y') }}</div>
                                                @if($tarea->proxima_fecha->isToday())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hoy</span>
                                                @elseif($tarea->proxima_fecha->isPast())
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Atrasado</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">Cada {{ $tarea->frecuencia_dias }} días</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($tarea->activa)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Inactiva</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('plantas.tareas.show', [$planta->id, $tarea->id]) }}" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('plantas.tareas.edit', [$planta->id, $tarea->id]) }}" class="text-yellow-600 hover:text-yellow-900 mr-3"><i class="fas fa-edit"></i></a>
                                                <form action="{{ route('plantas.tareas.destroy', [$planta->id, $tarea->id]) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('¿Estás seguro de eliminar esta tarea?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>