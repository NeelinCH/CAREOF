<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mis Plantas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-medium">Listado de Plantas</h3>
                        <a href="{{ route('plantas.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150">
                            <i class="fas fa-plus mr-2"></i> Nueva Planta
                        </a>
                    </div>

                    @if($plantas->isEmpty())
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No tienes plantas registradas. <a href="{{ route('plantas.create') }}" class="font-medium underline text-blue-700 hover:text-blue-600">Añade tu primera planta</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($plantas as $planta)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow duration-300">
                                    <!-- Reemplazar la sección de imagen en la tarjeta: -->
@if($planta->imagen)
    <img src="{{ asset('storage/' . $planta->imagen) }}" 
         alt="{{ $planta->nombre }}" 
         class="w-full h-48 object-cover">
@else
    <div class="w-full h-48 bg-gray-100 flex items-center justify-center">
        <i class="fas fa-leaf text-gray-400 text-5xl"></i>
    </div>
@endif
                                    <div class="p-4">
                                        <h4 class="font-bold text-xl mb-2">{{ $planta->nombre }}</h4>
                                        <p class="text-gray-700 mb-1"><strong>Especie:</strong> {{ $planta->especie }}</p>
                                        <p class="text-gray-700 mb-1"><strong>Ubicación:</strong> {{ $planta->ubicacion }}</p>
                                        <p class="text-gray-700 mb-3"><strong>Tareas:</strong> {{ $planta->tareas_count }}</p>
                                        
                                        <div class="flex justify-between items-center">
                                            <a href="{{ route('plantas.show', $planta->id) }}" class="text-sm bg-blue-500 hover:bg-blue-700 text-white py-1 px-3 rounded">
                                                <i class="fas fa-eye mr-1"></i> Ver
                                            </a>
                                            <a href="{{ route('plantas.edit', $planta->id) }}" class="text-sm bg-yellow-500 hover:bg-yellow-700 text-white py-1 px-3 rounded">
                                                <i class="fas fa-edit mr-1"></i> Editar
                                            </a>
                                            <form action="{{ route('plantas.destroy', $planta->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm bg-red-500 hover:bg-red-700 text-white py-1 px-3 rounded" onclick="return confirm('¿Estás seguro de eliminar esta planta?')">
                                                    <i class="fas fa-trash mr-1"></i> Eliminar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>