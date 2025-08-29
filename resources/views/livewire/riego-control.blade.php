<div class="bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <i class="fas fa-microchip mr-2 text-blue-500"></i>
        Control de Riego Automático
    </h3>
    
    @if($mensaje)
        <div class="mb-4 p-3 rounded bg-green-100 text-green-700 border border-green-200">
            {{ $mensaje }}
            @if($ultimaAccion)
                <br><span class="text-sm text-green-600">Última acción: {{ $ultimaAccion }}</span>
            @endif
        </div>
    @endif

    <!-- Estado del Arduino -->
    <div class="mb-4 p-3 rounded 
        {{ $estadoArduino === 'Disponible' ? 'bg-green-100 text-green-700 border border-green-200' : 
           'bg-red-100 text-red-700 border border-red-200' }}">
        <div class="flex items-center">
            <i class="fas {{ $estadoArduino === 'Disponible' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} mr-2"></i>
            <span class="font-medium">Estado Arduino:</span>
            <span class="ml-2">{{ $estadoArduino }}</span>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <button wire:click="activarRiego" 
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded flex items-center justify-center transition duration-200"
                {{ $estadoArduino !== 'Disponible' ? 'disabled' : '' }}>
            <i class="fas fa-play-circle mr-2"></i> Activar Riego
        </button>
        
        <button wire:click="$set('mostrarConfiguracion', true)"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded flex items-center justify-center transition duration-200">
            <i class="fas fa-cog mr-2"></i> Configuración
        </button>
    </div>

    <!-- Información del riego programado -->
    <div class="bg-gray-50 p-3 rounded">
        <p class="text-sm text-gray-600">
            <i class="fas fa-info-circle mr-1"></i>
            Configuración actual: {{ $tiempoRiego }}ms • {{ $cantidadMl }}ml • {{ $puerto }}
        </p>
    </div>

    <!-- Modal de configuración -->
    @if($mostrarConfiguracion)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4">
            <h4 class="text-lg font-semibold mb-4">Configuración de Arduino</h4>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Puerto COM</label>
                    <select wire:model="puerto" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="COM9">COM9</option>
                        <option value="COM10">COM10</option>
                        <option value="COM3">COM3</option>
                        <option value="COM4">COM4</option>
                        <option value="/dev/ttyUSB0">/dev/ttyUSB0 (Linux)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tiempo de riego (ms)</label>
                    <input type="number" wire:model="tiempoRiego" min="100" max="10000" step="100" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cantidad estimada (ml)</label>
                    <input type="number" wire:model="cantidadMl" min="1" max="5000" 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button wire:click="$set('mostrarConfiguracion', false)" 
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 transition duration-200">
                    Cancelar
                </button>
                <button wire:click="guardarConfiguracion" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>