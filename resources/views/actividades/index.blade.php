<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Historial de Actividades') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Todas mis actividades</h3>
                        <p class="text-sm text-gray-600">Historial completo de todas las acciones realizadas en el sistema</p>
                    </div>

                    @if($actividades->isEmpty())
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        No hay actividades registradas todavía.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($actividades as $actividad)
                                <div class="bg-gray-50 p-4 rounded-lg border-l-4 
                                    @switch($actividad->tipo)
                                        @case('riego') border-blue-400 @break
                                        @case('planta') border-green-400 @break
                                        @case('tarea') border-yellow-400 @break
                                        @default border-gray-400
                                    @endswitch">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 rounded-full flex items-center justify-center
                                                @switch($actividad->tipo)
                                                    @case('riego') bg-blue-100 text-blue-600 @break
                                                    @case('planta') bg-green-100 text-green-600 @break
                                                    @case('tarea') bg-yellow-100 text-yellow-600 @break
                                                    @default bg-gray-100 text-gray-600
                                                @endswitch">
                                                <i class="fas {{ $actividad->icono }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3 flex-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $actividad->user->name }}
                                                </p>
                                                <span class="text-xs text-gray-500">
                                                    {{ $actividad->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ $actividad->descripcion }}
                                                @if($actividad->planta)
                                                    <span class="font-medium">"{{ $actividad->planta->nombre }}"</span>
                                                @endif
                                            </p>
                                            @if($actividad->planta)
                                                <p class="text-xs text-gray-500 mt-1">
                                                    <i class="fas fa-leaf mr-1"></i>{{ $actividad->planta->especie }}
                                                    @if($actividad->planta->ubicacion)
                                                        · <i class="fas fa-map-marker-alt mr-1"></i>{{ $actividad->planta->ubicacion }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $actividades->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>