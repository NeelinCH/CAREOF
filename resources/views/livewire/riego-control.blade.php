<div class="bg-white p-6 rounded-lg shadow-md" wire:key="componente-principal">
    <h3 class="text-lg font-semibold mb-4 flex items-center">
        <i class="fas fa-microchip mr-2 text-blue-500"></i>
        Control de Riego Automático
    </h3>
    
    <!-- Mensajes de estado -->
    @if($mensaje)
        <div class="mb-4 p-3 rounded 
            {{ strpos($mensaje, '✅') === 0 ? 'bg-green-100 text-green-700 border border-green-200' : 
               'bg-red-100 text-red-700 border border-red-200' }}">
            <div class="flex items-center justify-between">
                <span>{{ $mensaje }}</span>
                <button wire:click="$set('mensaje', '')" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @if($ultimaAccion)
                <div class="text-sm mt-1">
                    Última acción: {{ $ultimaAccion }}
                </div>
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

    <!-- Configuración actual -->
    <div class="mb-4 bg-gray-50 p-4 rounded-lg">
        <h4 class="font-medium text-gray-700 mb-2">Configuración Actual</h4>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2 text-sm">
            <div>
                <span class="font-medium">Puerto:</span>
                <span class="block text-blue-600">{{ $puerto }}</span>
            </div>
            <div>
                <span class="font-medium">Tiempo:</span>
                <span class="block text-blue-600">{{ number_format($tiempoRiego) }} ms</span>
            </div>
            <div>
                <span class="font-medium">Cantidad:</span>
                <span class="block text-blue-600">{{ number_format($cantidadMl) }} ml</span>
            </div>
        </div>
    </div>

    <!-- Botones de acción PRINCIPALES (fuera del modal) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4" wire:key="botones-principales">
        <button wire:click="activarRiego" 
                wire:loading.attr="disabled"
                wire:key="boton-activar"
                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded flex items-center justify-center transition duration-200 disabled:opacity-50"
                {{ $estadoArduino !== 'Disponible' ? 'disabled' : '' }}
                {{ $mostrarConfiguracion ? 'disabled' : '' }}>
            <i class="fas fa-play-circle mr-2"></i> 
            <span wire:loading.remove>Activar Riego</span>
            <span wire:loading>Activando...</span>
        </button>
        
        <button wire:click="abrirConfiguracion"
                wire:key="boton-configurar"
                class="w-full bg-gray-500 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded flex items-center justify-center transition duration-200"
                {{ $mostrarConfiguracion ? 'disabled' : '' }}>
            <i class="fas fa-cog mr-2"></i> Configurar
        </button>
    </div>

    <!-- Modal de configuración -->
    @if($mostrarConfiguracion)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4" 
         wire:click="cancelarConfiguracion"> <!-- Cerrar al hacer clic fuera -->
        <div class="bg-white p-6 rounded-lg shadow-xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto" 
             wire:click.stop> <!-- Prevenir propagación dentro del modal -->
             
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold">Configuración de Arduino</h4>
                <button wire:click="cancelarConfiguracion" 
                        wire:loading.attr="disabled"
                        class="text-gray-500 hover:text-gray-700 disabled:opacity-50">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Puerto COM -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Puerto COM *</label>
                    <select wire:model="puertoTemp" 
                            wire:loading.attr="disabled"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 disabled:opacity-50">
                        <option value="">Seleccionar puerto...</option>
                        <option value="COM9">COM9</option>
                        <option value="COM10">COM10</option>
                        <option value="COM3">COM3</option>
                        <option value="COM4">COM4</option>
                        <option value="COM5">COM5</option>
                        <option value="COM6">COM6</option>
                        <option value="/dev/ttyUSB0">/dev/ttyUSB0 (Linux)</option>
                        <option value="/dev/ttyACM0">/dev/ttyACM0 (Linux)</option>
                    </select>
                    @error('puertoTemp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                
                <!-- Tiempo de riego -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Tiempo de riego (milisegundos) *
                    </label>
                    <input type="number" wire:model="tiempoRiegoTemp" 
                           wire:loading.attr="disabled"
                           min="100" max="10000" step="100" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 disabled:opacity-50">
                    @error('tiempoRiegoTemp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">1000 ms = 1 segundo</p>
                </div>
                
                <!-- Cantidad de agua -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad estimada de agua (ml) *
                    </label>
                    <input type="number" wire:model="cantidadMlTemp" 
                           wire:loading.attr="disabled"
                           min="1" max="5000" step="10"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 disabled:opacity-50">
                    @error('cantidadMlTemp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    <p class="text-xs text-gray-500 mt-1">1000 ml = 1 litro</p>
                </div>
            </div>

            <!-- Botones de acción del modal -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-200">
                <button wire:click="cancelarConfiguracion" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-200 disabled:opacity-50">
                    <i class="fas fa-times mr-1"></i> Cancelar
                </button>
                <button wire:click="guardarConfiguracion" 
                        wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition duration-200 disabled:opacity-50">
                    <i class="fas fa-save mr-1"></i> Guardar
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Escuchar eventos de Livewire
document.addEventListener('livewire:load', function() {
    // Prevenir que los clics dentro del modal se propaguen
    document.addEventListener('click', function(e) {
        const modal = document.querySelector('[wire\\:click="cancelarConfiguracion"]');
        const modalContent = document.querySelector('.bg-white.p-6.rounded-lg');
        
        if (modal && modalContent && modal.contains(e.target) && !modalContent.contains(e.target)) {
            Livewire.dispatch('cancelarConfiguracion');
        }
    });

    // Deshabilitar botones principales cuando el modal está abierto
    Livewire.on('mostrarConfiguracionUpdated', (value) => {
        const botones = document.querySelectorAll('button[wire\\:key="boton-activar"], button[wire\\:key="boton-configurar"]');
        botones.forEach(boton => {
            if (value) {
                boton.setAttribute('disabled', 'disabled');
                boton.classList.add('opacity-50');
            } else {
                boton.removeAttribute('disabled');
                boton.classList.remove('opacity-50');
            }
        });
    });
    
    // Limpiar mensaje después de tiempo
    Livewire.on('limpiarMensaje', () => {
        setTimeout(() => {
            // Esta función debe ser implementada en tu componente si es necesaria
            console.log('Limpiar mensaje automático');
        }, 2000);
    });
    
    // Notificaciones
    Livewire.on('notificacion', (data) => {
        showNotification(data.mensaje, data.tipo);
    });
});

// También puedes agregar este código para mayor seguridad
document.addEventListener('click', function(e) {
    // Si el clic fue en un botón dentro del modal, detener la propagación
    if (e.target.closest('.bg-white.p-6.rounded-lg')) {
        e.stopPropagation();
    }
});

// Función para mostrar notificaciones (debe estar definida en el layout principal)
function showNotification(message, type = 'success') {
    // Esta función debe estar definida en tu layout principal
    if (typeof window.showNotification === 'function') {
        window.showNotification(message, type);
    } else {
        // Implementación básica de respaldo
        alert(`${type.toUpperCase()}: ${message}`);
    }
}
</script>
@endpush