<div class="bg-gradient-to-r from-blue-50 to-green-50 p-6 rounded-xl border border-blue-200 shadow-lg">
    <!-- Mensaje de advertencia añadido aquí -->
    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl mr-3"></i>
            </div>
            <div>
                <h5 class="font-bold text-yellow-700 mb-1">Importante: Conexión Arduino en desarrollo</h5>
                <p class="text-yellow-600 text-sm">La funcionalidad de conexión con Arduino se encuentra actualmente en etapa de desarrollo y puede presentar fallos o comportamientos inesperados. Te recomendamos utilizar esta característica teniendo en cuenta la presente aclaración.</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between mb-6">
        <h4 class="font-bold text-xl flex items-center text-blue-700">
            <i class="fas fa-microchip mr-3 text-2xl"></i> 
            Control Arduino - Riego Automático
        </h4>
        
        <!-- Estado de conexión -->
        <div class="flex items-center space-x-3">
            @if($verificandoConexion)
                <div class="flex items-center text-yellow-600">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    <span class="text-sm font-medium">Verificando...</span>
                </div>
            @else
                <div class="flex items-center {{ $conectado ? 'text-green-600' : 'text-red-600' }}">
                    <div class="w-3 h-3 rounded-full {{ $conectado ? 'bg-green-500' : 'bg-red-500' }} animate-pulse mr-2"></div>
                    <span class="text-sm font-medium">{{ $conectado ? 'Conectado' : 'Desconectado' }}</span>
                </div>
            @endif
            
            <button wire:click="verificarConexion" 
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700 transition disabled:opacity-50">
                <i class="fas fa-sync-alt mr-1 {{ $verificandoConexion ? 'fa-spin' : '' }}"></i>
                Verificar
            </button>
        </div>
    </div>

    <!-- Mensajes -->
    @if($mensaje)
        <div class="mb-4 p-3 rounded-lg border {{ 
            $tipoMensaje === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 
            ($tipoMensaje === 'error' ? 'bg-red-50 border-red-200 text-red-800' : 
            ($tipoMensaje === 'warning' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : 
            'bg-blue-50 border-blue-200 text-blue-800'))
        }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas {{ 
                        $tipoMensaje === 'success' ? 'fa-check-circle' : 
                        ($tipoMensaje === 'error' ? 'fa-exclamation-triangle' : 
                        ($tipoMensaje === 'warning' ? 'fa-exclamation-circle' : 'fa-info-circle'))
                    }} mr-2"></i>
                    <span class="text-sm font-medium">{{ $mensaje }}</span>
                </div>
                <button wire:click="cerrarMensaje" 
                        class="text-gray-400 hover:text-gray-600 ml-2"
                        title="Cerrar mensaje">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @if($ultimaAccion)
                <div class="text-sm mt-1 opacity-75">
                    Última acción: {{ $ultimaAccion }}
                </div>
            @endif
        </div>
    @endif

    <!-- Estado del Arduino -->
    <div class="mb-4 p-3 rounded transition-all duration-300 border
        {{ $estadoArduino === 'Disponible' ? 'bg-blue-50 text-blue-700 border-blue-100' : 
           ($estadoArduino === 'Comunicación OK' ? 'bg-green-50 text-green-700 border-green-100' :
           ($estadoArduino === 'Regando...' ? 'bg-orange-50 text-orange-700 border-orange-100' :
           ($estadoArduino === 'Riego completado' ? 'bg-emerald-50 text-emerald-700 border-emerald-100' :
           'bg-gray-100 text-gray-700 border-gray-200'))) }}">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="flex items-center {{ $conectado ? 'text-green-600' : 'text-red-600' }}">
                    <div class="w-3 h-3 rounded-full {{ $conectado ? 'bg-green-500' : 'bg-red-500' }} 
                        {{ $regando ? 'animate-pulse' : '' }} mr-2"></div>
                    <span class="text-sm font-medium">Estado: {{ $estadoArduino }}</span>
                </div>
            </div>
            <div class="text-xs opacity-75">
                Puerto: {{ $puertoSeleccionado ?? 'No seleccionado' }}
            </div>
        </div>
    </div>

    <!-- Configuración de Puerto -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-cog mr-2 text-gray-600"></i>
            Configuración del Puerto
        </h5>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
        <!-- Selector de puerto -->
<div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Puerto Serie</label>
    <select wire:model="puertoSeleccionado" 
            class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        <option value="">Seleccionar puerto...</option>
        @foreach($puertosDisponibles as $puerto)
            <option value="{{ $puerto['puerto'] }}">
                {{ $puerto['puerto'] }} - {{ $puerto['descripcion'] }}
            </option>
        @endforeach
        <!-- Opciones adicionales -->
    </select>
    @error('puertoSeleccionado')
        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
    @enderror
</div>

            <!-- Botón escanear puertos -->
            <div class="flex flex-col justify-end">
                <button wire:click="escanearPuertos" 
                        wire:loading.attr="disabled"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-4 rounded transition disabled:opacity-50 flex items-center justify-center">
                    <i class="fas {{ $escaneandoPuertos ? 'fa-spinner fa-spin' : 'fa-search' }} mr-2"></i>
                    {{ $escaneandoPuertos ? 'Escaneando...' : 'Escanear Puertos' }}
                </button>
            </div>

            <!-- Botón test comunicación -->
            <div class="flex flex-col justify-end">
                <button wire:click="testComunicacion" 
                        wire:loading.attr="disabled"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded transition disabled:opacity-50 flex items-center justify-center">
                    <i class="fas fa-vial mr-2"></i>
                    Test Comunicación
                </button>
            </div>
        </div>
    </div>

    <!-- Configuración del Riego -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
        <h5 class="font-semibold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-sliders-h mr-2 text-gray-600"></i>
            Configuración del Riego
        </h5>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Cantidad de agua -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cantidad de Agua (ml)
                    <span class="text-gray-500 text-xs">(50-5000ml)</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           wire:model="cantidadMl" 
                           min="50" 
                           max="5000" 
                           step="50"
                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-12">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 text-sm">ml</span>
                    </div>
                </div>
                @error('cantidadMl')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tiempo de riego -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tiempo de Riego (ms)
                    <span class="text-gray-500 text-xs">(1000-30000ms)</span>
                </label>
                <div class="relative">
                    <input type="number" 
                           wire:model="tiempoRiego" 
                           min="1000" 
                           max="30000" 
                           step="500"
                           class="w-full rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 pr-12">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <span class="text-gray-500 text-sm">ms</span>
                    </div>
                </div>
                @error('tiempoRiego')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
                <div class="text-xs text-gray-500 mt-1">
                    ≈ {{ round($tiempoRiego / 1000, 1) }} segundos
                </div>
            </div>
        </div>

        <!-- Valores predefinidos -->
        <div class="mt-3">
            <label class="block text-sm font-medium text-gray-700 mb-2">Configuraciones rápidas:</label>
            <div class="flex flex-wrap gap-2">
                <button type="button" 
                        wire:click="$set('cantidadMl', 250); $set('tiempoRiego', 1500)"
                        class="px-3 py-1 bg-green-100 text-green-800 rounded text-xs hover:bg-green-200 transition">
                    Ligero (250ml)
                </button>
                <button type="button" 
                        wire:click="$set('cantidadMl', 500); $set('tiempoRiego', 2000)"
                        class="px-3 py-1 bg-blue-100 text-blue-800 rounded text-xs hover:bg-blue-200 transition">
                    Normal (500ml)
                </button>
                <button type="button" 
                        wire:click="$set('cantidadMl', 1000); $set('tiempoRiego', 3000)"
                        class="px-3 py-1 bg-orange-100 text-orange-800 rounded text-xs hover:bg-orange-200 transition">
                    Abundante (1000ml)
                </button>
            </div>
        </div>
    </div>

    <!-- Botón de Activar Riego -->
    <div class="bg-white p-4 rounded-lg border border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h5 class="font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-play-circle mr-2 text-green-600"></i>
                    Activar Riego Automático
                </h5>
                <p class="text-sm text-gray-600 mt-1">
                    Se regará con <strong>{{ $cantidadMl }}ml</strong> durante <strong>{{ round($tiempoRiego / 1000, 1) }}s</strong>
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button wire:click="resetForm" 
                        type="button"
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded transition">
                    <i class="fas fa-undo mr-2"></i>
                    Resetear
                </button>
                
                <button wire:click="activarRiego" 
                        wire:loading.attr="disabled"
                        {{ !$conectado || !$puertoSeleccionado ? 'disabled' : '' }}
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition flex items-center">
                    @if($regando)
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Regando...
                    @else
                        <i class="fas fa-tint mr-2"></i>
                        Activar Riego
                    @endif
                </button>
            </div>
        </div>

        @if(!$conectado)
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center text-red-700">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span class="text-sm">Arduino no conectado. Verifica la conexión y el puerto seleccionado.</span>
                </div>
            </div>
        @elseif(!$puertoSeleccionado)
            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center text-yellow-700">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="text-sm">Selecciona un puerto para continuar.</span>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Escuchar evento de riego completado
    window.addEventListener('riego-completado', event => {
        // Mostrar notificación
        if (typeof toastr !== 'undefined') {
            toastr.success(event.detail.message);
        }
        
        // Opcional: recargar la página después de 3 segundos
        setTimeout(() => {
            if (confirm('¿Deseas recargar la página para ver el registro actualizado?')) {
                window.location.reload();
            }
        }, 3000);
    });
});
</script>
@endpush