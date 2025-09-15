<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalles de Planta') }}: {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Información de la Planta -->
                        <div class="md:col-span-1">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                @if($planta->imagen)
                                    <img src="{{ asset('storage/' . $planta->imagen) }}" 
                                         alt="{{ $planta->nombre }}" 
                                         class="w-full h-64 object-cover rounded-md mb-4">
                                @else
                                    <div class="w-full h-64 bg-gray-100 flex items-center justify-center rounded-md mb-4">
                                        <i class="fas fa-leaf text-gray-400 text-8xl"></i>
                                    </div>
                                @endif
                                
                                <h3 class="text-lg font-medium mb-2">{{ $planta->nombre }}</h3>
                                <p class="text-gray-600 mb-1"><strong>Especie:</strong> {{ $planta->especie }}</p>
                                <p class="text-gray-600 mb-1"><strong>Ubicación:</strong> {{ $planta->ubicacion }}</p>
                                <p class="text-gray-600 mb-3"><strong>Fecha adquisición:</strong> {{ $planta->fecha_adquisicion->format('d/m/Y') }}</p>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('plantas.edit', $planta->id) }}" 
                                       class="flex-1 text-center bg-yellow-500 hover:bg-yellow-600 text-white py-2 px-4 rounded flex items-center justify-center">
                                        <i class="fas fa-edit mr-2"></i> Editar
                                    </a>
                                    <form action="{{ route('plantas.destroy', $planta->id) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded flex items-center justify-center" 
                                                onclick="return confirm('¿Estás seguro de eliminar esta planta?')">
                                            <i class="fas fa-trash mr-2"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Tareas de la Planta -->
                        <div class="md:col-span-2">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Tareas Programadas</h3>
                                <a href="{{ route('plantas.tareas.create', $planta->id) }}" class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                                    <i class="fas fa-plus mr-1"></i> Nueva Tarea
                                </a>
                            </div>

                            @if($planta->tareas->isEmpty())
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
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Próxima Fecha</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Frecuencia</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($planta->tareas as $tarea)
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
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        {{ $tarea->proxima_fecha->format('d/m/Y') }}
                                                        @if($tarea->proxima_fecha->isToday())
                                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Hoy</span>
                                                        @elseif($tarea->proxima_fecha->isPast())
                                                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Atrasado</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        Cada {{ $tarea->frecuencia_dias }} días
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
        </div>
    </div>
</x-app-layout>