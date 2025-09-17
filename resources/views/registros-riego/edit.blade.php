<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Registro de Riego') }} - {{ $planta->nombre }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-xl">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <!-- Header con navegación -->
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <a href="{{ route('plantas.tareas.registros.index', [$planta->id, $tarea->id]) }}" 
                               class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                                <i class="fas fa-arrow-left mr-1"></i> Volver a registros
                            </a>
                            <h3 class="text-lg font-semibold mt-2 text-gray-800">
                                Editar registro de riego
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                Tarea: {{ ucfirst($tarea->tipo) }} - Planta: {{ $planta->nombre }}
                            </p>
                        </div>
                    </div>

                    <!-- Mensaje de función no disponible -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-tools text-yellow-400 text-2xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-yellow-800">
                                    Función en Desarrollo
                                </h3>
                                <div class="mt-2 text-yellow-700">
                                    <p>
                                        La edición de registros de riego se encuentra actualmente en desarrollo 
                                        y no está disponible en esta versión de la aplicación.
                                    </p>
                                    <p class="mt-2">
                                        <strong>Próximamente</strong> podrás editar toda la información de tus registros 
                                        de riego de manera completa y sencilla.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información del registro actual (solo lectura) -->
                    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                        <h4 class="font-bold text-lg mb-4 text-gray-700">
                            <i class="fas fa-info-circle mr-2"></i> Información del Registro Actual
                        </h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Cantidad</label>
                                <p class="text-gray-900 font-medium">{{ $registroRiego->cantidad_ml ?? '0' }} ml</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Método</label>
                                <p class="text-gray-900 font-medium">{{ $registroRiego->metodo ?? 'No especificado' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Fecha y Hora</label>
                                <p class="text-gray-900 font-medium">
                                    {{ $registroRiego->fecha_hora->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500 mb-1">Registrado por</label>
                                <p class="text-gray-900 font-medium">{{ $registroRiego->user->name }}</p>
                            </div>
                        </div>
                        
                        @if($registroRiego->observaciones)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">Observaciones</label>
                            <p class="text-gray-900 bg-white p-3 rounded border border-gray-200">
                                {{ $registroRiego->observaciones }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Alternativas disponibles -->
                    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
                        <h4 class="font-bold text-lg mb-4 text-blue-700">
                            <i class="fas fa-lightbulb mr-2"></i> Alternativas Disponibles
                        </h4>
                        
                        <div class="space-y-3">
                            <p class="text-blue-800">
                                Mientras desarrollamos esta función, puedes:
                            </p>
                            
                            <ul class="list-disc list-inside text-blue-700 space-y-2 pl-4">
                                <li>Crear un nuevo registro con la información corregida</li>
                                <li>Eliminar este registro si contiene información incorrecta</li>
                                <li>Contactar al administrador para correcciones urgentes</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4 flex space-x-3">
                            <a href="{{ route('plantas.tareas.registros.create', [$planta->id, $tarea->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-2"></i> Crear Nuevo Registro
                            </a>
                            
                            <form action="{{ route('plantas.tareas.registros.destroy', [$planta->id, $tarea->id, $registroRiego->id]) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition" 
                                        onclick="return confirm('¿Estás seguro de eliminar este registro? Esta acción no se puede deshacer.')">
                                    <i class="fas fa-trash mr-2"></i> Eliminar Registro
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Información de contacto para desarrollo -->
                    <div class="mt-6 p-4 bg-gray-100 rounded-lg text-center">
                        <p class="text-sm text-gray-600">
                            <i class="fas fa-code-branch mr-1"></i> 
                            ¿Necesitas esta función urgentemente? 
                            <a href="mailto:desarrollo@careof.com" class="text-blue-600 hover:text-blue-800 underline">
                                Contacta al equipo de desarrollo
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .border-b:last-child {
            border-bottom: none !important;
        }
    </style>
    @endpush
</x-app-layout>